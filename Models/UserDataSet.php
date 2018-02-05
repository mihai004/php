<?php
include ('UserData.php');
require('Database.php');
class UserDataSet
{
    protected $_dbHandle, $_dbInstance;

    /**
     * UserDataSet constructor.
     */
    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    /**
     * The method in called when a user types its e-mail in order to log in.
     * If the user is found, then it may proceed further. Otherwise a message will
     * be displayed.
     * @param $field
     * @param $value
     * @return array|UserData
     */
    public function searchUser($field, $value) {
        $sqlQuery = "SELECT * FROM Users Where $field = '$value'";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        $dataSet = [];
        while($row = $statement->fetch()) {
            $dataSet = new UserData($row);
        }
        return $dataSet;
    }


    /**
     *  The method is invoked when a person wants to register. In this case, all the data that the
     * user types will be tested in a securely manner before adding that persons' details to the database.
     */
     public function insertUser()
    {
            $eMail = $mobileNumber = $houseNumber = $streetName = $city = $country = $postCode = $password = null;

            $eMail = $this->test_input($_POST['emailReg']);
            $mobileNumber = $this->test_input($_POST['mobileNumber']);
            $houseNumber = $this->test_input($_POST['houseNumber']);
            $streetName = $this->test_input($_POST['streetName']);
            $city = $this->test_input($_POST['city']);
            $country = $this->test_input($_POST['country']);
            $regPostcode = "/^([a-zA-Z]){1}([0-9][0-9]|[0-9]|[a-zA-Z][0-9][a-zA-Z]|[a-zA-Z][0-9][0-9]|[a-zA-Z][0-9]){1}([ ])([0-9][a-zA-z][a-zA-z]){1}$/";
            $postCode = $this->test_input($_POST['postCode']);
            $password = $this->test_input($_POST['password']);
            $passwordConfirm = $this->test_input($_POST['passwordConfirm']);

            if ($this->checkEmail($eMail) == true) {
                echo '<script>alert("We already have your eMail")</script>';
            } else if (!(filter_var($eMail, FILTER_VALIDATE_EMAIL))) {
                echo '<script>alert("Invalid eMail format")</script>';
            } else if ($this->checkPhoneNumber($mobileNumber)) {
                echo '<script>alert("Oops! Someone is already using your mobile ")</script>';
            } else if (!(is_numeric($mobileNumber))) {
                echo '<script>alert("Invalid mobile number format")</script>';
            } else if (!(is_numeric($houseNumber))) {
                echo '<script>alert("Invalid house number format")</script>';
            } else if (!preg_match("/^[a-zA-Z ]*$/", $streetName)) {
                echo '<script>alert("Only letters and white space allowed for Street Name")</script>';
            } else if (!preg_match("/^[a-zA-Z ]*$/", $city)) {
                echo '<script>alert("Only letters and white space allowed for City Name")</script>';
            } else if (!preg_match("/^[a-zA-Z ]*$/", $country)) {
                echo '<script>alert("Only letters and white space allowed for Country Name")</script>';
            } else if (!preg_match($regPostcode, $postCode)) {
                echo '<script>alert("Invalid post code format")</script>';
            } else if ($password != $passwordConfirm) {
                echo '<script>alert("Passwords do not match")</script>';
            } else if(!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/', $password)) {
                echo '<script>alert("The password should be at least 6 - 32 characters long." +
                    "\\nHaving at least one capital letter and one digit")</script>';
            }
            else {

                $confirmCode = rand(); // check e-mail in database, no duplicates
                $salt = "alabalaportocala";
                $password .= $salt;
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $message = "
            Confirm your E-mail
            Click the link below
            http://eqp326.edu.csesalford.com/emailConfirm.php?eMail=$eMail&code=$confirmCode
            ";
                mail($eMail, 'DoNotReply@myBooks.com', $message, "Hi");

                $sqlQuery = "INSERT INTO Users (eMail, phoneNumber, houseNumber, streetName, city, country, 
                      postCode, password, confirmed, confirmCode) VALUES ('$eMail', '$mobileNumber', '$houseNumber', 
                      '$streetName', '$city', '$country', '$postCode', '$passwordHash', 0, '$confirmCode')";
                $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
                $statement->execute(); // execute the PDO statement

                echo '<p style="font-size: 20px; margin-bottom: 15px;"  class="bg-success text-center text-success">
                      Registration Complete. Please confirm your e-Mail!
            </br></p>';
            }
    }

    /**
     * The method tests the user input in a securely manner.
     * @param $data
     * @return string
     */
    public function test_input($data) {
        if(empty($data) || is_null($data))
        {
            echo '<script>alert("You need to complete all the fields in order to proceed!")</script>';
        }
        else {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
    }

    /**
     * The method checks to see if a user with a e-Mail inserted for registering exist or not.
     * @param $enteredEmail
     * @return bool
     */
    public function checkEmail($enteredEmail){
        $sqlQuery = "SELECT eMail FROM Users Where eMail = '$enteredEmail'";
        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement
        if($statement->rowCount() > 0){
            return true;
        }
    }

    /**
     * The method checks to see if a mobile phone number is used by someone else.
     * @param $mobileNumber
     * @return bool
     */
    public function checkPhoneNumber($mobileNumber){
        $sqlQuery = "SELECT phoneNumber FROM Users Where phoneNumber = '$mobileNumber'";
        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement
        if($statement->rowCount() > 0){
            return true;
        }
    }

    /**
     * The method is invoked when a user clicks the link send to its e-Mail and hence it is
     * able to log in, while the confirmation code remains in the database for further use when for
     * instance a user would want to reset its password.
     * @param $field
     * @param $value
     * @param $eMail
     */
    public function updateUser($field, $value, $eMail){

        $sqlQuery = "UPDATE Users SET $field = '$value' WHERE eMail = '$eMail'";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        if( $statement->execute() ){
        }
        else {
            echo 'Something went wrong';
        }
    }

    /**
     * The method is called to see if a user has confirmed the link with the security code attached or not.
     * @param $_eMail
     * @param $_insertCode
     * @return bool
     */
    public function checkConfirmation($_eMail, $_insertCode){
        $userFound = $this->searchUser('eMail', $_eMail);
        $_code = $userFound->getConfirmCode();
        if($_code == $_insertCode) {
            $this->updateUser('confirmed', '1', $_eMail);
            return true;
        }
        else {
            return false;
        }

    }

}