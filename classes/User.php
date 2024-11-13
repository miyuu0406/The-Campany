<?php
    /*
        include         - include again and again every time you refresh the page
        include_once    - include only once
        require         - require again and again and will stop the script when there's error
        require_once    - require once only and will stop the script when there's error
    */
    require_once "Database.php";
    class User extends Database
    {
        // store() - Insert record
        public function store($request)
        {
            $first_name = $request['first_name'];
            $last_name  = $request['last_name'];
            $username   = $request['username'];
            $password   = $request['password'];
            $password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (first_name, last_name, username, password)
                    VALUES ('$first_name', '$last_name', '$username', '$password')";
            if ($this->conn->query($sql)){
                header('location: ../views');   // go to index.php or the login page
                exit;                           // same as die
            } else {
                die('Error creating the user: ' . $this->conn->error);
            }
        }


        public function login($request)
        {
            $username = $request['username'];
            $password = $request['password'];
            $sql = "SELECT * FROM users WHERE username = '$username'";
            $result = $this->conn->query($sql);
            # CHECK the username
            if($result->num_rows == 1){
                # CHECK if the password is correct
                $user = $result->fetch_assoc();


                if(password_verify($password, $user['password'])){
                    # CREATE the session variables
                    session_start();
                    $_SESSION['id']         = $user['id'];
                    $_SESSION['username']   = $user['username'];
                    $_SESSION['full_name']  = $user['first_name'] . " " . $user['last_name'];
                    header('location: ../views/dashboard.php');
                    exit;
                } else {
                    die('Password is incorrect');
                }
            } else {
                die('Username not found');
            }
        }


        public function logout()
        {
              session_start();
              session_unset();
              session_destroy();

              header('location: ../views');
              exit;
        }

        public function getAllUsers()
        {
            $sql = "SELECT id, first_name,last_name,username, photo FROM users";

            if($result = $this->conn->query($sql)){
                return $result;
            }else{
                die('ERROR IN retrieving all users: ' . $this->conn->error);
            }
        }

        public function getUser($id)
        {
            $sql = "SELECT * FROM users WHERE id = $id";

            if($result = $this->conn->query($sql)){
                return $result->fetch_assoc();
            }else{
                die('ERROR IN retrieving the users: ' . $this->conn->error);
            }
        }
        
        public function update($request, $files)
        {
            session_start();
            $id             = $_SESSION['id'];
            $first_name     = $request['first_name'];
            $last_name      = $request['last_name'];
            $username       = $request['username'];
            $photo          = $files['photo']['name']; // woman1.jpg  // holds the name of the image
            $tmp_photo      = $files['photo']['tmp_name']; // holds the image from the temporary storage
            // ['photo']    is the name of the form
            // ['name']     is the name of the image
            // ['tmp_name'] is the temporary storage of the image
            $sql = "UPDATE users
                    SET first_name = '$first_name',
                        last_name = '$last_name',
                        username = '$username'
                    WHERE id = '$id'";
            if($this->conn->query($sql)){
                $_SESSION['username'] = $username;
                $_SESSION['full_name'] = "$first_name $last_name";
                # IF there is an uploaded photo, save it to the database and save the files to the images folder
                if($photo){
                    $sql = "UPDATE users SET photo = '$photo' WHERE id = '$id'";
                    $destination = "../assets/images/$photo";
                    // SAVE the image name to the DB
                    if($this->conn->query($sql)){
                        // SAVE the file to the images folder
                        if(move_uploaded_file($tmp_photo, $destination)){
                            header('location: ../views/dashboard.php');
                            exit;
                        } else {
                            die('ERROR in moving the photo.');
                        }
                    } else {
                        die('ERROR in uploading the photo: ' . $this->conn->error);
                    }
                }
                header('location: ../views/dashboard.php');
                exit;
            } else {
                die('ERROR in updating the user: ' . $this->conn->error);
            }
        }

public function delete()
{
    session_start();
    $id = $_SESSION['id'];

    $sql = "DELETE FROM users WHERE id = '$id'";

    if($this->conn->query($sql)){
        $this->logout();

    }else{
        die("ERROR in deleting your account: " . $this->conn->error);
    }
}
        


    }
?>