<!-- ================================================================================================================================ -->


<!-- PHP Mailer Sender -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NUB Group | Home | Admin</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/ico_header1.png">

    <!-- Core Bootstrap Css -->
    <link rel="stylesheet" href="../assets/dist/css/bootstrap.min.css">

    <!-- Core MDB Css -->
    <link rel="stylesheet" href="../assets/md/css/mdb.min.css">

    <!-- Core MDB Css -->
    <link rel="stylesheet" href="../assets/md/css/style.css">

    <!-- Icon styles -->
    <link rel="stylesheet" href="../assets/icons/style.css">

    <!-- Icon styles -->
    <link rel="stylesheet" href="../../assets/style.min.css">

    <!-- Internal CSS -->
    <style type="text/css">
        .top{
            border-top-right-radius:5px;
            border-top-left-radius:5px;
        }
        .bottom{
            border-top: 5px solid white;
        }
        ul{
            list-style:none;
            margin-left: -40px;

        }
        ul li{
            float:left;
            width:110px;
            height:65px;
            line-height:65px;
            text-align: center;
        }
        ul li a{
            display:block;
            color:black;
            font-family:Century Gothic;
            transition: border 1s ease-in-out;
        }
        ul li a:hover{
        	border-bottom: 3px solid #2196f3;  /*color : #2196f3*/
        	color:#2196f3;
        	font-weight: bold;
        }
        .active{
        	border-bottom: 3px solid #2196f3;
        	color:#2196f3;
        	font-weight: bold;
        }
        /* input[type="submit"]{
        	background-color: #2196f3;
        	outline: none;
        	border:none;
        	height: 30px;
        	width: 80px;
        	text-align: center;
        	color:white;
        	border-radius: 5px;
        	margin-top: 10px;
        } */
        tr th{
            font-weight: bold;
            /* font-style: italic; */
        }
        .withdraw-modal{
            display: none;
        }
        .showup{
            display: block;
            animation: modalanim 1s cubic-bezier(0.165, 0.84, 0.44, 1) 0s 1 alternate forwards;
        }
        @keyframes modalanim{
            from{
                transform: translateY(200px);
            }
            to{
                transform: translateY(0px);
            }
        }
        .well1{
        background-color: #fff;
        /* width:240px;
        height:240px; */
        display:-webkit-box;
        display:-ms-flexbox;
        display:flex;
        -webkit-box-pack:center;
        -ms-flex-pack:center;
        justify-content:center;
        -webkit-box-align:center;
        -ms-flex-align:center;
        align-items:center;
        border-radius:5%}
        .well{
          border-radius:10px;
        }
        body{
          background-color: black;
          /* background-image: url("../pictures/logo_nub_blured.jpg"); */
          width: 100%;
          height: 100vh;
          background: blur(9);
          background-repeat: no-repeat;
          background-position: 85%;
          background-size: cover;
        }
          .table-cell tr th{
            padding: 8px;
            background-color: #2196f3;
            color: #fff;
          }
          .table-cell tr td{
            padding-bottom: 2px;
            max-width: 30px;
          }
          .fm{
            padding: 6px 12px 6px 6px;
            color: #333;
            background-color: #eee;
            border: 1px solid #ddd;
            border-radius: 5px;

            /*replacing default styling*/
            /* appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none; */
          }
          .fm:focus,.fm:hover{
            outline: none;
            border: 1px solid #2196f3;
            color: #2196f3;
          }
          .form-control:focus,.form-control:hover{
            outline: none;
            border: 1px solid #2196f3;
            color: #2196f3;
          }
          .select option{
            background: #f5f5f5;
          }
          .proforma-title{
            background-color: #2196f3;
            color :#fff;
            padding-top: 10px;
            border: 1px solid #2196f3;
          }
          .proforma_ul{
            margin-left: 75px;
          }
</style>

</head>
<body style="">
    <br>
    <div class="container-fluid" style="margin-top: -4vh">
    	<div class="row">
    		<div class="col-md-12 blue p-3 top">
          <div class="row">
            <div class="col-md-11 blue-text text-center">
              <img src="../pictures/logo_nub_home.svg" style="height: 70px;width: 420px;">
            </div>
            <div class="col-md-1">
              <a href="index.php?msg" title="Eplore Other Date"><span class="text-center text-white ml-3" data-icon="&#xe023;"></span></a>
            </div>
          </div>
        </div>
    	</div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-2 blue p-2 bottom">
                        <h3 class="white-text text-center" style="font-family:Caviar Dreams; line-height:38px;">
                          <img src="../pictures/logo_nub_admin.svg" style="height: 38px; width: 68px;">
                                      </h3>
                    </div>
