<?php

session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: admin.php");
    exit;
}
require_once "konfigurimi.php";

// Definoni variablat dhe inicializoni me vlera të zbrazeta.
$username = $password = "";
$username_err = $password_err = "";


// Procesimi i dhënave të formës kur forma është paraqitur (submitted).
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Kontrolloni nëse emri i përdoruesit (username) është i zbrazët.
    if(empty(trim($_POST["username"]))){
        $username_err = "Ju lutemi shkruani emrin e p&euml;rdoruesit!";
    } else{
        $username = trim($_POST["username"]);
    }

    // Kontrolloni nëse fjalëkalimi është i zbrazët.
    if(empty(trim($_POST["password"]))){
        $password_err = "Ju lutem vendosni fjal&euml;kalimin tuaj!";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validoni kredencialet.
    if(empty($username_err) && empty($password_err)){

        // Përgatitni një deklaratë (select) të zgjedhur.
        $sql = "SELECT id, username, password FROM administratori WHERE username = ?";

        // Përgatitni një deklaratë SQL për ekzekutim.
        if($stmt = mysqli_prepare($link, $sql)){

            // Bind (vendos) variablat në deklaratën e përgatitur si parametra.
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Vendosni parametrat.
            $param_username = $username;

            // Përpjekje për të ekzekutuar deklaratën e përgatitur.
            // Ekzekuton një Query të përgatitur.
            if(mysqli_stmt_execute($stmt)){

                // Ruaj rezultatin.
                mysqli_stmt_store_result($stmt);

                // Kontrolloni nëse ekziston një përdorues,
                // nëse po, atëherë verifiko fjalëkalimin.
                if(mysqli_stmt_num_rows($stmt) == 1){    

                    // Bind(vendos) rezultatet e variablave.
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    // mysqli_stmt_fetch - Merrni rezultatet nga një deklaratë e përgatitur
                    //	në variablet e lidhura.
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){

                            // Fjalëkalimi është i saktë, për nisur një sesion të ri.
                            session_start();

                            //Ruaj të dhënat në variablat e sesionit.
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            

                            // Përcjell përdoruesin në faqen kryesore index.php.
                            header("location: admin.php");
                        } else{

                            // Shfaq një mesazh gabimi nëse fjalëkalimi nuk është valid.
                            $password_err = "Fjalekalimi q&euml; shenuat nuk ishte i vlefsh&euml;m!";
                        }
                    }
                } else{

                    // Shfaq një mesazh gabimi nëse emri i përdoruesit nuk ekziston.
                    $username_err = "Nuk u gjet asnj&euml; llogari me at&euml; em&euml;r p&euml;rdoruesi!";
                }
            } else{
                echo "Gabim ju lutem provoni p&euml;rs&euml;ri me von&euml;.";
            }
        }

        // Mbyll deklaraten (statement).
        mysqli_stmt_close($stmt);
    }

    // Mbyll koneksionin (lidhjen).
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">

        <title>Admin - Week of Women &copy; </title>
         <link rel = "icon" type = "image/png" href = "../2020/Photos/logo.png">
        <link rel = "apple-touch-icon" type = "image/png" href="../2020/Photos/logo.png"/>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.0/css/bootstrap.min.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <style type="text/css">
            .center {
                margin: auto;
                margin-top: 100px;
                width: 100%;
                padding: 10px;
            }
            .form-group {
                width: 30%;
                margin-left: auto;
                margin-right: auto;
            }
            #bg-nav{
                width: 100%;
                height: 100%;
                background-image: url(../2020/Photos/backgroundblur.jpg);
                background-size: cover;
                color: #FFF;         
            }
            @media only screen and (max-width: 768px) {
                .form-group {
                    width: 100%;
                    margin-left: auto;
                    margin-right: auto;
                } 

                {
        </style>
    </head>
    <body id="bg-nav">

        <div class="center">
            <center> <h2>Administrator</h2> </center> 

            <center> <p>Only specific people are allowed to enter this page.</p> </center>


            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Admin:</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>  

                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>

                <div class="form-group">
                    <center><input type="submit" class="btn btn-success" value="Log in">
                    </center>  
                </div>

            </form>
        </div>    
        <center><a href="../2020/index.php"><button class="btn btn-primary">Return Home</button></a></center>


        <script src="js/particles.js"></script>
        <script src="js/app.js"></script>

    </body>
</html>