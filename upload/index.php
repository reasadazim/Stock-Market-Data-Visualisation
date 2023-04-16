<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <!-- AJAX -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>

<style>
.form-wrapper {
    border: 1px solid gainsboro;
    border-radius: 10px;
    padding: 20px;
    -webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.25);
    -moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.25);
    box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.25);
}
.form-header{
    padding: 20px;
    margin-top: 20vh; 
    text-align:center;  
}
.alert-success{
    display:none;
}
.alert-danger{
    display:none;
}
</style>

<body>
<div class="container">
    <div class="row">
        <div class="col-xl-3">
        </div>
        <div class="col-xl-6">
            <div class="row form-header">
                <div class="col">
                    <h4>Upload CSV file here</h4>
                </div>
            </div>
            <div class="form-wrapper">
                <!-- CSV File Upload Form -->
                <!-- <form action="../api/upload.php" method="post" enctype="multipart/form-data"> -->
                <form action="../api/upload.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Select CSV file</label>
                    <input name="file" type="file" class="form-control file">
                </div>
                <div class="mb-3">
                    <label for="inputState" class="form-label">Duration</label>
                    <select name="duration" id="inputState" class="form-select duration">
                        <option selected>Choose...</option>
                        <option value="1d">1 day</option>
                        <option value="1m">1 Month</option>
                        <option value="1w">1 Week</option>
                        <option value="3m">3 Months</option>
                        <option value="6m">6 Months</option>
                        <option value="12m">1 Year</option>
                    </select>
                </div>
                <button type="submit" name="submit" class="btn btn-primary submit-button">Submit</button>
                </form>
                <!-- END - CSV File Upload Form -->
            </div>
        </div>
        <div class="col-xl-3">
        </div>
    </div>
</div>

<br>

<div class="container">
    <div class="row">
        <div class="col-xl-3"></div>
        <div class="col-xl-6">
            <!-- Alert Success-->
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>File uploaded successfully.</strong> 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <!-- END - Alert -->

            <!-- Alert Error-->
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error uploading file.</strong> 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <!-- END - Alert -->
        </div>
        <div class="col-xl-3"></div>
    </div>
</div>



<script>
    $(document).ready(function(){

        $(".alert-success").hide();

        $(".alert-danger").hide();

        if(window.location.hash == "#success"){
            $(".alert-success").show();
        }

        if(window.location.hash == "#error"){
            $(".alert-danger").show();
        }
    });
</script>
</body>
</html>