<!doctype html>
<html>
<head>
    <title>Audio</title>
</head>
<body>

<script>
    function play() {
        var audio = document.getElementById("audio");
        audio.play();
    }
</script>

<input type="button" value="PLAY" onclick="play()">
<audio id="audio" src="../sounds/apertura_porta_treno.mp3"></audio>

</body>
</html>