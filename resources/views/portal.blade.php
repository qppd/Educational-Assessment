<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script src="{{ url('faceapi/face-api.min.js') }}"></script>

<!------ Include the above in your HEAD tag ---------->
<!DOCTYPE html>
<html>

<head>
    <title>TOPCIT</title>
    <link rel="icon" href="{{ url('storage/images/topcitlogo.png') }}">
</head>


<style type="text/css">
    /* BASIC */

    html,
    body {
        background: #ffffff;
        background-image: url('{{ url('images/webapplogo.png') }}');
        background-size: 100% 100%;
        background-repeat: no-repeat;
        height: 100%;
        width: 100%;
        font-family: 'Numans', sans-serif;
    }

    body {
        font-family: "Poppins", sans-serif;
        height: 100vh;
        width: 100vw;
    }

    a {
        color: #92badd;
        display: inline-block;
        text-decoration: none;
        font-weight: 400;
    }

    h2 {
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
        margin: 40px 8px 10px 8px;
        color: #cccccc;
    }

    /* STRUCTURE */

    .wrapper {
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
        width: 100%;
        min-height: 100%;
        padding: 20px;
    }

    #formContent {
        -webkit-border-radius: 10px 10px 10px 10px;
        border-radius: 10px 10px 10px 10px;
        background: #252525;
        padding: 30px;
        opacity: 0.9;
        top: 20px;

        left: 28%;
        /* Reset the left property */
        width: 90%;
        max-width: 450px;
        position: relative;
        padding: 0px;
        -webkit-box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
        box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    #formFooter {
        background-color: #f6f6f6;
        border-top: 1px solid #dce8f1;
        padding: 25px;
        text-align: center;
        -webkit-border-radius: 0 0 10px 10px;
        border-radius: 0 0 10px 10px;
    }



    /* TABS */

    h2.inactive {
        color: #cccccc;
    }

    h2.active {
        color: #0d0d0d;
        border-bottom: 2px solid #5fbae9;
    }



    /* FORM TYPOGRAPHY*/

    input[type=button],
    input[type=submit],
    input[type=reset] {
        background-color: #56baed;
        border: none;
        color: white;
        padding: 15px 80px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        text-transform: uppercase;
        font-size: 13px;
        -webkit-box-shadow: 0 10px 30px 0 rgba(95, 186, 233, 0.4);
        box-shadow: 0 10px 30px 0 rgba(95, 186, 233, 0.4);
        -webkit-border-radius: 5px 5px 5px 5px;
        border-radius: 5px 5px 5px 5px;
        margin: 5px 20px 40px 20px;
        -webkit-transition: all 0.3s ease-in-out;
        -moz-transition: all 0.3s ease-in-out;
        -ms-transition: all 0.3s ease-in-out;
        -o-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }

    input[type=button]:hover,
    input[type=submit]:hover,
    input[type=reset]:hover {
        background-color: #39ace7;
    }

    input[type=button]:active,
    input[type=submit]:active,
    input[type=reset]:active {
        -moz-transform: scale(0.95);
        -webkit-transform: scale(0.95);
        -o-transform: scale(0.95);
        -ms-transform: scale(0.95);
        transform: scale(0.95);
    }

    input[type=text] {
        background-color: #f6f6f6;
        border: none;
        color: #0d0d0d;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 5px;
        width: 85%;
        border: 2px solid #f6f6f6;
        -webkit-transition: all 0.5s ease-in-out;
        -moz-transition: all 0.5s ease-in-out;
        -ms-transition: all 0.5s ease-in-out;
        -o-transition: all 0.5s ease-in-out;
        transition: all 0.5s ease-in-out;
        -webkit-border-radius: 5px 5px 5px 5px;
        border-radius: 5px 5px 5px 5px;
    }

    input[type=password] {
        background-color: #f6f6f6;
        border: none;
        color: #0d0d0d;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 5px;
        width: 85%;
        border: 2px solid #f6f6f6;
        -webkit-transition: all 0.5s ease-in-out;
        -moz-transition: all 0.5s ease-in-out;
        -ms-transition: all 0.5s ease-in-out;
        -o-transition: all 0.5s ease-in-out;
        transition: all 0.5s ease-in-out;
        -webkit-border-radius: 5px 5px 5px 5px;
        border-radius: 5px 5px 5px 5px;
    }

    input[type=text]:focus {
        background-color: #fff;
        border-bottom: 2px solid #5fbae9;
    }

    input[type=text]:placeholder {
        color: #cccccc;
    }



    /* ANIMATIONS */

    /* Simple CSS3 Fade-in-down Animation */
    .fadeInDown {
        -webkit-animation-name: fadeInDown;
        animation-name: fadeInDown;
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    @-webkit-keyframes fadeInDown {
        0% {
            opacity: 0;
            -webkit-transform: translate3d(0, -100%, 0);
            transform: translate3d(0, -100%, 0);
        }

        100% {
            opacity: 1;
            -webkit-transform: none;
            transform: none;
        }
    }

    @keyframes fadeInDown {
        0% {
            opacity: 0;
            -webkit-transform: translate3d(0, -100%, 0);
            transform: translate3d(0, -100%, 0);
        }

        100% {
            opacity: 1;
            -webkit-transform: none;
            transform: none;
        }
    }

    /* Simple CSS3 Fade-in Animation */
    @-webkit-keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @-moz-keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .fadeIn {
        opacity: 0;
        -webkit-animation: fadeIn ease-in 1;
        -moz-animation: fadeIn ease-in 1;
        animation: fadeIn ease-in 1;

        -webkit-animation-fill-mode: forwards;
        -moz-animation-fill-mode: forwards;
        animation-fill-mode: forwards;

        -webkit-animation-duration: 1s;
        -moz-animation-duration: 1s;
        animation-duration: 1s;
    }

    .fadeIn.first {
        -webkit-animation-delay: 0.4s;
        -moz-animation-delay: 0.4s;
        animation-delay: 0.4s;
    }

    .fadeIn.second {
        -webkit-animation-delay: 0.6s;
        -moz-animation-delay: 0.6s;
        animation-delay: 0.6s;
    }

    .fadeIn.third {
        -webkit-animation-delay: 0.8s;
        -moz-animation-delay: 0.8s;
        animation-delay: 0.8s;
    }

    .fadeIn.fourth {
        -webkit-animation-delay: 1s;
        -moz-animation-delay: 1s;
        animation-delay: 1s;
    }

    /* Simple CSS3 Fade-in Animation */
    .underlineHover:after {
        display: block;
        left: 0;
        bottom: -10px;
        width: 0;
        height: 2px;
        background-color: #56baed;
        content: "";
        transition: width 0.2s;
    }

    .underlineHover:hover {
        color: #0d0d0d;
    }

    .underlineHover:hover:after {
        width: 100%;
    }

    h1 {
        color: #60a0ff;
    }

    /* OTHERS */

    *:focus {
        outline: none;
    }

    #icon {
        width: 30%;
    }

    .brand_logo {
        margin: 10px;
        width: 200px;
        height: 100px;

    }


    .flat-line {

        /* other styles */
        background: repeating-linear-gradient(90deg,
                transparent,
                transparent 10px,
                rgba(0, 0, 0, 0.1) 10px,
                rgba(0, 0, 0, 0.1) 20px);
    }

    @keyframes pulse {
        0% {
            height: 8px;
            opacity: 1;
        }

        50% {
            height: 16px;
            opacity: 0.5;
        }

        100% {
            height: 8px;
            opacity: 1;
        }
    }

    .flat-line {
        /* other styles */
        animation: pulse 1s ease-in-out infinite;
    }

    .flat-line {
        /* other styles */
        position: absolute;
        left: 15%;
        top: 50%;
    }





    @keyframes warning {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<style>
    #cameraView {
        width: 300px;
        height: 250px;
        margin: 0 auto;
        background: black;
        border: 5px solid black;
        border-radius: 10px;
        box-shadow: 0 5px 50px #333
    }
</style>


<body>

    <div class="fadeIn first">

    </div>
    <div class="wrapper fadeInDown">

        <div id="formContent">

            <div class="fadeIn first">
                <img src="{{ url('storage/images/topcitlogo.png') }}" class="brand_logo" id="icon" alt="Logo">
            </div>

            <form action="/portal/login" method="POST">
                @csrf
                <div id="cameraContainer">
                    <canvas id="faceCanvas" width="300" height="250" style="position: absolute;"></canvas>
                    <video id="cameraView" autoplay playsinline muted></video>

                </div>
                <input type="hidden" class="fadeIn second" id="username" name="username" placeholder="Username"
                    required>

                <input type="hidden" id="password" class="fadeIn third" name="password" placeholder="Password"
                    required>
                <a href="portal/forgot" id="forgot" class="fadeIn fourth">Forgot Password</a>

                <input type="hidden" id="submit" class="fadeIn fourth" value="LOG IN">

                <a href="portal/register" id="register" class="fadeIn fourth">Don't have an account? Register now!</a>


            </form>

            @if ($errors->any())
                <div class='alert alert-danger alert-dismissible'>
                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                    <h4><i class='icon fa fa-warning'></i> Error!</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session()->has('success'))
                <div class='alert alert-success alert-dismissible'>
                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                    <h4><i class='icon fa fa-check'></i> Success!</h4>
                    <ul>
                        {{ session()->get('success') }}
                    </ul>
                </div>
            @endif
        </div>
    </div>
</body>

@include('includes/scripts')

<script>
    const video = document.getElementById("cameraView");
    const canvas = document.getElementById("faceCanvas");


    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri("/weights"),
        faceapi.nets.faceLandmark68Net.loadFromUri("/weights"),
        faceapi.nets.faceRecognitionNet.loadFromUri("/weights"),
        faceapi.nets.faceLandmark68Net.loadFromUri("/weights"),
    ]).then(startWebcam);

    function startWebcam() {
        navigator.mediaDevices
            .getUserMedia({
                video: true,
                audio: false,
            })
            .then((stream) => {
                video.srcObject = stream;
            })
            .catch((error) => {
                console.error(error);
            });
    }

    function getLabeledFaceDescriptions() {
        var labels = @json($studentUsernames);
        console.log(labels);
        return Promise.all(
            labels.map(async (label) => {
                const descriptions = [];
                for (let i = 1; i <= 2; i++) {

                    const assetLink = `{{ asset('storage/images/students/${label}/${i}.jpg') }}`;
                    console.log("LINK:" + assetLink);

                    const image = await faceapi.fetchImage(assetLink);

                    const detections = await faceapi
                        .detectSingleFace(image)
                        .withFaceLandmarks()
                        .withFaceDescriptor();
                    descriptions.push(detections.descriptor);
                }
                return new faceapi.LabeledFaceDescriptors(label, descriptions);
            })
        );
    }

    let faceRecognitionInterval;
    video.addEventListener("play", async () => {
        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);
        const displaySize = {
            width: 300,
            height: 250
        };

        faceRecognitionInterval = setInterval(async () => {
            const detections = await faceapi
                .detectAllFaces(video)
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resizedDetections = faceapi.resizeResults(detections, displaySize);

            canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
            const results = resizedDetections.map((d) => {
                return faceMatcher.findBestMatch(d.descriptor);
            });

            results.forEach(async (result, i) => {
                const label = result._label;
                const box = resizedDetections[i].detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, {
                    label: label,
                });

                console.log(result._distance);
                if (result._distance < 0.5) {
                    drawBox.draw(canvas);
                    // Make an HTTP request to find the user in the Laravel database
                    try {
                        const response = await fetch(`/find-user/${label}`);
                        if (response.ok) {
                            const userData = await response.json();
                            // Handle the user data from the database as needed
                            console.log('User data:', userData);

                            if (userData) {

                                document.getElementById('password').type =
                                    'password';

                                document.getElementById('submit').type =
                                    'submit'; // Change 'hidden' to 'text'

                                // Change the value of input username to userData.username
                                document.getElementById('username').value = userData
                                    .username;


                                document.getElementById('cameraContainer').style
                                    .display =
                                    'none';

                                clearInterval(faceRecognitionInterval);
                            }
                        } else {
                            console.error("Error:" + response);
                        }
                    } catch (error) {
                        console.error('Error while making the request:', error);
                    }
                }


            });
        }, 100);
    });
</script>


</html>
