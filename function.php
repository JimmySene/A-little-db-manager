<?php

function getTables($ip, $bdd, $user, $pass) // RETURN TABLES AND CREATE SESSION WITH DB INFORMATIONS SENT BY CONNECTION FORM
{
    $_SESSION['ip'] = $ip;
    $_SESSION['bdd'] = $bdd;
    $_SESSION['user'] = $user;
    $_SESSION['pass'] = $pass;

    
    try {
        $bdd = new PDO("mysql:host=$ip;dbname=$bdd;charset=utf8",$user,$pass);
    } catch(PDOException $e) {
        echo $e->getMessage();
    }
    $req = $bdd->prepare("SHOW TABLES");
    $req->execute();

    return $req;
}

function getFields($nameTable) // RETURN FIELDS OF THE TABLE $nameTable
{

    extract($_SESSION);

    $bdd = new PDO("mysql:host=$ip;dbname=$bdd;charset=utf8",$user,$pass);
    $req = $bdd->query("DESCRIBE $nameTable");
    $result = $req->fetchAll(PDO::FETCH_ASSOC);

    return $result;

}

function getEntries($nameTable) // RETURN DATA OF THE TABLE $nameTable
{
    extract($_SESSION);

    $bdd = new PDO("mysql:host=$ip;dbname=$bdd;charset=utf8",$user,$pass);
    $req = $bdd->prepare("SELECT * FROM $nameTable");
    $req->execute(array($nameTable));
    $result = $req->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}

function addEntry($entry) // ADD ENTRY IN THE TABLE $entry['table']
{
    extract($_SESSION);
    $nameTable = $entry['table'];
    unset($entry['table']);
    unset($entry['add_entry']);
    $nb_fields = count($entry);
    $i = 1;
    $string = "";

    foreach($entry as $value) // COMPLETE STRING FOR VALUES SQL
    {
        if($i != $nb_fields) {

            $string = $string . "'$value',";
            $i++;

        } else {
            
            $string = $string . "'$value'";

        }   
    }

    $bdd = new PDO("mysql:host=$ip;dbname=$bdd;charset=utf8",$user,$pass);
    $bdd->exec("INSERT INTO $nameTable VALUES(null,$string)");

    header("location:index.php?table=$nameTable");

}

function deleteEntry($nameTable, $idEntry, $fieldPK)
{
    extract($_SESSION);
    $bdd = new PDO("mysql:host=$ip;dbname=$bdd;charset=utf8",$user,$pass);
    $bdd->exec("DELETE FROM $nameTable WHERE $fieldPK = '$idEntry'");

    header("location:index.php?table=$nameTable");
}

function getEntry($nameTable, $idEntry, $fieldPK)
{
    extract($_SESSION);
    $bdd = new PDO("mysql:host=$ip;dbname=$bdd;charset=utf8",$user,$pass);
    $req = $bdd->query("SELECT * FROM $nameTable WHERE $fieldPK = '$idEntry'");
    $result = $req->fetch();

    return $result;
}

function modifEntry($entry) // MODIF ENTRY IN THE TABLE $entry['table']
{
    extract($_SESSION);
    $nameTable = $entry['table'];
    $id = $entry['id'];
    $nameFieldPK = $entry['fieldPK'];

    unset($entry['table']);
    unset($entry['modif_entry']);
    unset($entry['id']);
    unset($entry['fieldPK']);

    $nb_fields = count($entry);
    $i = 1;
    $string = "";

    foreach($entry as $key => $value) // COMPLETE STRING FOR VALUES SQL
    {
        if($i != $nb_fields) {

            $string = $string .  "$key = '$value', ";
            $i++;

        } else {
            
            $string = $string . "$key = '$value'";

        }   
    }


    $bdd = new PDO("mysql:host=$ip;dbname=$bdd;charset=utf8",$user,$pass);
    $bdd->exec("UPDATE $nameTable SET $string WHERE $nameFieldPK = $id");
    echo $string;
    header("location:index.php?table=$nameTable");

}

function deconnection() // DESTROY SESSION AND BACK TO FORM CONNECTION
{
    session_destroy();
    header('location:index.php');
}

