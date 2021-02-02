
var gauges = {};

//------------------------------------------------------------------------------------
// ON LOAD
//------------------------------------------------------------------------------------

window.onload = onLoad;

function onLoad() {

    connectPowerUp();

    initButtons();

    initGauges();

}

function updateGauge(
    num,
    speed
){
    gauges[num].set(speed);
}

function initGauges(){

    $("canvas[data-type=gauge-speed]").each( function(i,target) {

        let div = $(target).parents("div.train-control-block[data-type=control-train]").first();
        let num = div.attr("data-num");

        let max_speed = $(target).attr("data-max-speed");
        let high_speed = $(target).attr("data-high-speed");
        let medium_speed = $(target).attr("data-medium-speed");

        let opts = {
            angle: -0.25,
            lineWidth: 0.2,
            pointer: {
                length: 0.6,
                strokeWidth: 0.05,
                color: '#000000'
            },
            staticZones: [
                {strokeStyle: "#F03E3E", min: -max_speed, max: -high_speed},
                {strokeStyle: "#FFDD00", min: -high_speed, max: -medium_speed},
                {strokeStyle: "#30B32D", min: -medium_speed, max: medium_speed},
                {strokeStyle: "#FFDD00", min: medium_speed, max: high_speed},
                {strokeStyle: "#F03E3E", min: high_speed, max: max_speed}
            ],
            staticLabels: {
                font: "10px sans-serif",  // Specifies font
                labels: [-max_speed, -high_speed, -medium_speed, 0, +medium_speed, +high_speed, +max_speed],  // Print labels at these values
                //labels: [-max_speed, -high_speed, -medium_speed, 0, medium_speed, high_speed, max_speed],  // Print labels at these values
                color: "#000000",  // Optional: Label text color
                fractionDigits: 0  // Optional: Numerical precision. 0=round off.
            },
            limitMax: false,
            limitMin: false,
            strokeColor: '#E0E0E0',
            highDpiSupport: true
        };
        gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
        gauge.minValue = -max_speed; // set max gauge value
        gauge.maxValue = max_speed; // set max gauge value
        gauge.animationSpeed = 32; // set animation speed (32 is default value)
        let value = parseInt(div.attr("data-engine-value"));
        gauge.set(value); // set actual value
        gauges[num] = gauge;
    });
}

function connectPowerUp() {

    $("div[data-type=lego-powered-up]").each( function(i,e) {
        let set = $(e).attr("data-set");
        let textarea = $(e).find("textarea[data-type=log]");
        let command = 'connect_power_up';
        if (textarea) {
            $.ajax({
                url: "./ajax/lego-train-ajax.php",
                data: {
                    command:command,
                },
                type: "POST",
                success: function(response) {
                    textarea.append(response);
                    textarea.append("&#13;&#10;");
                },
                error: function(error) {
                    textarea.append(error);
                    textarea.append("&#13;&#10;");
                }
            });
        }

    });
}

function showPanel(obj) {

    let jq = $(obj);

    let panel_id = jq.attr("data-id");

    $("div[data-type=panel]").each( function(i,e) {
        let div_id = $(e).attr("data-id");
        if (div_id === panel_id){
            $(e).show();
        } else {
            $(e).hide();
        }
    });

    jq.parents("ul.navbar-nav").first().find("li").each( function(i,e) {
        let li = $(e);
        if (li.find("a[data-type=nav-item]").first().attr("data-id") === panel_id ) {
            li.addClass("active");
        } else {
            li.removeClass("active");
        }
    });

    initGauges();
}

function initButtons() {

    $("button[data-type=action]").click( function() {

        let button = $(this);
        let action = button.attr("data-action");
        let div = button.parents("div.train-control-block[data-type=control-train]").first();

        let max_speed = parseInt(div.attr("data-engine-max-value"));
        let speed = parseInt(div.attr("data-engine-value"));
        let num = parseInt(div.attr("data-num"));

        let data = {};

        switch (action) {
            case "+":
                if (speed < max_speed) {
                    speed++;
                }
                div.attr("data-engine-value", speed);
                data["urls"] = div.attr("data-engine-urls");
                data["value"] = speed;
                break;
            case "-":
                if (speed > -max_speed) {
                    speed--;
                }
                div.attr("data-engine-value", speed);
                data["urls"] = div.attr("data-engine-urls");
                data["value"] = speed;
                break;
            case "stop":
                speed = 0;
                div.attr("data-engine-value", speed);
                data["urls"] = div.attr("data-engine-urls");
                data["value"] = speed;
                break;
            case "light-on":
                data["urls"] = div.attr("data-light-" + button.attr("data-light-num") + "-urls");
                data["value"] = max_speed;
                $(this).attr("data-action", "light-off");
                $(this).removeClass("btn-outline-danger");
                $(this).addClass("btn-success");
                break;
            case "light-off":
                data["urls"] = div.attr("data-light-" + button.attr("data-light-num") + "-urls");
                data["value"] = 0;
                $(this).attr("data-action", "light-on");
                $(this).removeClass("btn-success");
                $(this).addClass("btn-outline-danger");
                break;
        }

        let urls = [];
        let urls_by_type = JSON.parse(data["urls"]);
        for (let type in urls_by_type) {
            let urls_type = urls_by_type[type];
            for (let i in urls_type) {
                let url = urls_type[i];
                if (type == 0){
                    urls.push(url.replace('{{value}}',data["value"]));
                } else {
                    urls.push(url.replace('{{value}}',-1*data["value"]));
                }
            }
        }

        updateGauge(num, speed);

        let textarea = div.find("textarea[data-type=log]");
        let command = 'execute_actions';

        $.ajax({
            url: "./ajax/lego-train-ajax.php",
            data: {
                command:command,
                urls:urls
            },
            type: 'POST',
            success: function(response) {
                textarea.append(response);
                textarea.append("&#13;&#10;");
            },
            error: function(error) {
                textarea.append(error);
                textarea.append("&#13;&#10;");
            }
        });

    });

}

//-------------------------------------------------------------------------------------------------
// Controller Configuration Panel
//-------------------------------------------------------------------------------------------------

function configuration_name_changed(dom) {
    let jq = $(dom);
    let old_value = jq.attr("data-old-value");
    let value = jq.val();
    if (value === undefined) {
        value = jq.html();
    }
    if (value !== old_value){
        show_save_config_button(jq);
    }
}

function controller_config_changed(dom) {
    let jq = $(dom);
    let old_value = jq.attr("data-old-value");
    let value = jq.val();
    if (value === undefined) {
        value = jq.html();
    }
    let div = jq.parents("div.card[data-type=config-controller]").first();
    let save_btn = div.find("button[data-action=save-config]").first();
    if (value !== old_value){
        save_btn.show();
        show_save_config_button(jq);
    } else {
        save_btn.hide();
    }
}

//-------------------------------------------------------------------------------------------------
// Train Configuration Panel
//-------------------------------------------------------------------------------------------------

function train_config_changed(dom) {

    let jq = $(dom);
    let old_value = jq.attr("data-old-value");
    let value = jq.val();
    if (value === undefined) {
        value = jq.html();
    }
    let div = jq.parents("div.card[data-type=config-train]").first();
    let save_btn = div.find("button[data-action=save-config]").first();
    if (value !== old_value){
        save_btn.show();
        show_save_config_button(jq);
    } else {
        save_btn.hide();
    }

    let tbody = jq.parents("table").first().find("tbody").first();
    tbody.find("tr[data-type=controller]").each(function(i,e){
        let tr_controller = $(e);
        let elem = tr_controller.attr("data-elem");
        let num = tr_controller.attr("data-num");
        let controller = tr_controller.find("select").first().val();
        if (controller.length > 0) {
            let tr_system = tbody.find("tr[data-elem=" + elem + "][data-num=" + num + "][data-type=system]").first();
            tr_system.show();
            let selected_system = tr_system.find("select").first().val();
            tbody.find("tr[data-elem=" + elem + "][data-num=" + num + "]").each(function(j,f){
                let system = $(f).attr("data-system");
                if (system) {
                    if (system === selected_system){
                        if (selected_system.length > 0){
                            $(f).show();
                        } else {
                            $(f).hide();
                        }
                    } else {
                        $(f).hide();
                    }
                }
            });
        } else {
            tbody.find("tr[data-elem=" + elem + "][data-num=" + num + "]").each(function(i,e){
                if( $(e).attr("data-type") !== "controller"){
                    $(e).hide();
                }
                console.log($(e).attr("data-type"));
            });
        }
    });
}

function show_save_config_button(jq) {
    let configuration_card = jq.parents("div.card[data-type=configuration]").first();
    let button = configuration_card.find("button[data-action=save-config]").first();
    button.show();
}

function hide_all_save_config_buttons() {
    $("button[data-action=save-config]").each(function(i,e){
        $(e).hide();
    });
}

function move_train_up(dom) {
    move(dom, 'train', -1);
}

function move_train_down(dom) {
    move(dom, 'train', 1);
}

function move_controller_up(dom) {
    move(dom, 'controller', -1);
}

function move_controller_down(dom) {
    move(dom, 'controller', 1);
}

function move(
    dom,
    type,
    delta
) {

    let jq = $(dom);
    let src_card = jq.parents("div.card[data-type=config-" + type + "]").first();

    let src_num = parseInt(src_card.attr("data-" + type + "-num"));
    let dst_num = src_num + delta;

    let dst_card = src_card.parent().find("div.card[data-type=config-" + type + "][data-" + type + "-num=" + dst_num + "]").first();

    let src_table = src_card.find("table[data-type="+type+"-config]").first();
    let dst_table = dst_card.find("table[data-type="+type+"-config]").first();

    let src_table_html = src_table.html();

    src_table.html(dst_table.html());
    dst_table.html(src_table_html);

    src_card.find("button[data-action=save-config]").first().show();
    dst_card.find("button[data-action=save-config]").first().show();

    show_save_config_button(src_card);

}

function toggle_card(dom) {
    let button = $(dom);
    let card_body = button.parents("div.card").first().find("div.card-body").first();
    let span = button.find("span");
    if (span.hasClass("fa-caret-up")){
        card_body.hide();
        span.removeClass("fa-caret-up");
        span.addClass("fa-caret-down");
    } else {
        card_body.show();
        span.removeClass("fa-caret-down");
        span.addClass("fa-caret-up");
    }
}

//-------------------------------------------------------------------------------------------------
// Configuration Panel
//-------------------------------------------------------------------------------------------------

function parse_input_name(
    elems,
    value,
    array
) {
    let key = elems[0];
    if (elems.length === 1) {
        array[key] = value;
    } else {
        if (!(key in array)) {
            array[key] = {};
        }
        elems.shift();
        parse_input_name(
            elems,
            value,
            array[key]
        )
    }
}

function get_inputed_data_in_obj(
    obj,
    array
) {
    obj.find("input[data-type=config], select[data-type=config], textarea[data-type=config]").each( function(i,e) {
        let elems = $(e).attr("data-id").split("-");
        let value = $(e).val();
        parse_input_name(
            elems,
            value,
            array
        );
    });
}

function save_configuration(obj) {
    let jq = $(obj);
    let configuration_div = jq.parents("div.card[data-type=configuration]").first();

    let config = {};

    // Get configuration name
    config.name = configuration_div.find("input[data-type=configuration-name]").first().val();

    // Get controller configurations
    config.controllers = [];
    configuration_div.find("div.card[data-type=config-controller]").each( function(i,e) {
        controller_num = parseInt($(e).attr("data-controller-num"));
        config.controllers[controller_num] = {};
        get_inputed_data_in_obj(
            $(e),
            config.controllers[controller_num]
        );
    });

    // Get train configurations
    config.trains = [];
    configuration_div.find("div.card[data-type=config-train]").each( function(i,e) {
        train_num = parseInt($(e).attr("data-num"));
        config.trains[train_num] = {};
        get_inputed_data_in_obj(
            $(e),
            config.trains[train_num]
        );
    });

    command = "save_configuration";

    $.ajax({
        url: "./ajax/lego-train-ajax.php",
        data: {
            command:command,
            config:config
        },
        type: 'POST',
        success: function(html) {
            hide_all_save_config_buttons();
            reload_control_panel();
        },
        error: function(error) {

        }
    });

}

function reload_control_panel() {

    let div = $(this).parents("body").first().find("div[data-id=control]").first();

    command = "load_control_panel";

    $.ajax({
        url: "./ajax/lego-train-ajax.php",
        data: {
            command:command
        },
        type: 'POST',
        success: function(html) {
            div.html(html);
        },
        error: function(error) {

        }
    });

}

//-------------------------------------------------------------------------------------------------
// Add new controller/train
//-------------------------------------------------------------------------------------------------

function add_controller(dom) {
    add_config_elem_by_type(dom, "controller");
}

function add_train(dom) {
    add_config_elem_by_type(dom, "train");
}

function add_config_elem_by_type(dom, type) {
    let jq = $(dom);
    let landing_card = jq.parents("div.card-body").first();
    let command = "add_new_" + type;
    let num = landing_card.find("div.card[data-type=config-" + type + "]").length;
    $.ajax({
        url: "./ajax/lego-train-ajax.php",
        data: {
            command:command,
            num:num,
            is_first:(num===0),
            is_last:(num===0),
        },
        type: 'POST',
        success: function(html) {
            landing_card.append(html);
            window.location.href = "#"+type+"-num-"+num;
            show_save_config_button(jq);
        },
        error: function(error) {
            landing_card.append(error);
        }
    });
}

//-------------------------------------------------------------------------------------------------
// Delete controller/train
//-------------------------------------------------------------------------------------------------

function delete_controller(dom) {
    delete_config_elem_by_type(dom, "controller");
}

function delete_train(dom) {
    delete_config_elem_by_type(dom, "train");
}

function delete_config_elem_by_type(dom, type) {
    let jq = $(dom);
    let card = jq.parents("div.card[data-type=config-" + type + "]");
    let num = card.attr("data-" + type + "-num");
    let card_body = card.parents("div.card-body").first();
    num++;
    let next_card = card_body.find("div.card[data-"+type+"-num=" + num +"]");
    while ( next_card.length ) {
        let button = next_card.find("button[data-action=move-up]").first();
        button.click();
        num++;
        next_card = card_body.find("div.card[data-"+type+"-num=" + num +"]");
    }
    num--;
    card = card_body.find("div.card[data-"+type+"-num=" + num +"]");
    card.remove();
    let a = card_body.find("a[id="+type+"-num-" + num +"]");
    a.remove();
    if (num > 0) {
        num--;
        let card = card_body.find("div.card[data-"+type+"-num=" + num +"]");
        let button = card.find("button[data-action=move-down]").first();
        button.hide();
        if (num === 0){
            let button = card.find("button[data-action=move-up]").first();
            button.hide();
        }
    }

    show_save_config_button(jq);
}

function play_sound(dom) {
    $("audio[data-type=audio]").each( function (i,e) {
        e.pause();
        e.currentTime = 0;
    });
    $("audio[data-id=" + $(dom).val() + "]").get()[0].play();
}

function play_train_sound(audio) {
    $("audio[data-type=audio]").each( function (i,e) {
        e.pause();
        e.currentTime = 0;
    });
    $("audio[data-id=" + audio + "]").get()[0].play();
}