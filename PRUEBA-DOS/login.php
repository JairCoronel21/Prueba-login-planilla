<?php



session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: welcome.php");
  exit;
}
 
require_once 'database/config.php';

    $username = $password = "";
    $username_err = $password_err = "";
    



if($_SERVER["REQUEST_METHOD"] == "POST"){
 
 
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor ingrese su usuario o correo.";
    } else{
        $username = trim($_POST["username"]);
    }
    
   
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingrese su contraseña.";
    } else{
        $password = trim($_POST["password"]);
    }

    $captcha = $_POST['g-recaptcha-response'];
    $secret = '6Lcv7akhAAAAAAFVsKn1L8JpGwt3gBlk2lseul9J';

    if(!$captcha){

        echo '<script language="javascript">alert("Por favor verifique el captcha");</script>';
        
        } else {
        
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");
        
        $arr = json_decode($response, TRUE);
        
        if(empty($username_err) && empty($password_err)){
       
            // $sql = "SELECT id, username, password FROM users WHERE username = ?";
            // hasheando con mi funcion  
            
            $sql = "CALL SP_Login(?,?)";
            if($stmt = mysqli_prepare($link, $sql)){
              
                //mysqli_stmt_bind_param($stmt, "s", $param_username);
                mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
               
                // Set parameters de mysqli
                $param_username = $username;
                $param_password = $password;
                
                //Ejeccutar el statement
                if(mysqli_stmt_execute($stmt)){
    
                    mysqli_stmt_store_result($stmt);
                    
                   // Checkea si existe una fila
                    if(mysqli_stmt_num_rows($stmt) == 1){                    
                        
                        // Llena datos en las varibales
                        mysqli_stmt_bind_result($stmt, $credentials,$id);
                        // Obtiene los resultados de una sentencia preparadas en las variables vinculadas
                        if (mysqli_stmt_fetch($stmt)) {
                            if ($credentials) {
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                
                                header("location: welcome.php");
                            } else {
                                $password_err = "La contraseña que has ingresado es incorrecta.";
                            }
                        }
                        
                    } else{
                        
                        $username_err = "No existe cuenta registrada con ese nombre de usuario o correo electronico.";
                        
                    }
                } else{
                    echo "Algo salió mal, por favor vuelve a intentarlo.";
                }
            }
            
          
            mysqli_stmt_close($stmt);
        }


        if($arr['success']){
            echo '<h2>Thanks</h2>';
            } else {
            echo '<h3>Error al comprobar Captcha </h3>';
        }
    }

    // VALIDA LAS CREDENCIALES
    
    
  
    mysqli_close($link);
 }

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .contenedor{ width: 1000px; max-width: 1000px; margin:0 auto; background: skyblue;} 
    </style>
    
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximun-scale=1.0, minimal-scale=1.0">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="contenedor">
        <h2>Softgold  Inicio de Sesión</h2>
        <p>Por favor, complete sus credenciales para iniciar sesión.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
               
                <input placeholder="Ingrese correo electronico o nombre de usuario: " type="text" name="username" class="form-control" value="<?php echo $username;?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
               
                <input placeholder="Ingrese su contraseña: " type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            
            <!-- Crear el captcha -->
            <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="6Lcv7akhAAAAAAc-pPCutQ0GUKkhDUmu2an-h4d9"></div>
            </div>

             
            <div class="form-group">
                <!-- El boton Ingresar -->
                <input type="submit" class="btn btn-primary" value="Ingresar">
            </div>
            <p>¿Has olvidado tu contraseña? <a href="recuperar.php">Ingrese aqui</a>.</p>
            <p>¿No tienes una cuenta? <a href="register.php">Regístrate ahora</a>.</p>
        </form>
    </div>    
</body>
</html>