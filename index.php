<?php session_start();
//session_destroy();
include('function.php'); ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>
            A little DB Manager
        </title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>
    
    <?php 
    if(isset($_GET['deconnection'])) // DECONNECTION
    {
        deconnection();
    }
    if(isset($_POST['add_entry'])) // ADD ENTRY
    {
        addEntry($_POST);
    } 
    elseif(isset($_POST['modif_entry'])) // MODIF ENTRY
    {
        modifEntry($_POST);
    }
    elseif(isset($_GET['sup']) && isset($_GET['field_pk']) && isset($_GET['table'])) // DELETE ENTRY WITH ID $_GET['sup']
    {
        deleteEntry($_GET['table'], $_GET['sup'], $_GET['field_pk']);
    }
    
    elseif(isset($_GET['table'])) { // DISPLAY FIELDS AND DATA OF THE TABLE 
        $table = htmlspecialchars($_GET['table']); 
        $primaryKey = 0; $i = 0; $nameFieldPK = ""; ?>

        <h1>Table <?=$table?></h1>

       <table>
            <tr>
            <?php $fields = getFields($table);
          

            foreach($fields as $field)
            { 
                if($field['Key'] == 'PRI') { // SAVE FIELD PRIMARY KEY (PK) TO KNOW IT AND USE BELOW
                    $primaryKey = $i; 
                    $nameFieldPK = $field['Field'];
                }
                else 
                    $i++; ?>

                <th>
                   <?=$field['Field']?>
                </th>
            <?php } ?>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>

            <?php
            
            $entries = getEntries($table); 
            $i = 0; $j = 0;
            $id = array();

            foreach($entries as $entry)
            { ?>
               <tr>
                    <?php foreach($entry as $data) { // DISPLAY EACH ENTRY'S FIELDS
                        if($i == $primaryKey) { // IF FIELD PK SO SAVE ID IN ARRAY
                            $id[] = $data;
                            $i++; }
                        else
                            $i++; ?>
                        <td><?=$data?></td>
                    <?php } $i = 0;?>
                    <td><a href="index.php?modif=<?=$id[$j]?>&table=<?=$table?>">Modifier</a></td>
                    <td><a href="index.php?sup=<?=$id[$j]?>&table=<?=$table?>&field_pk=<?=$nameFieldPK?>">Supprimer</a></td>
               </tr> 
            <?php 
           $j++; } ?>
       
        </table>

        <p><a href="index.php">Voir les tables (retour)</a></p>

        <?php if(isset($_GET['modif'])) 
                $theEntry = getEntry($table, $_GET['modif'], $nameFieldPK); ?>

        <form action="index.php" method="post">
            <?php foreach($fields as $field) { 
                    if($field['Key'] != 'PRI') { // CHECK IF FIELD IS NOT PRIMARY KEY 
                    $nameField = $field['Field']?>
                        <label for="<?=$nameField?>"><?=$nameField?></label> <input type="text" name="<?=$nameField?>" id="<?=$nameField?>" 
                        value="<?php if(isset($_GET['modif'])) { echo $theEntry[$nameField]; } ?>" />
                <?php }
            } ?>
            <input type="hidden" name="table" value="<?=$table?>" />
        <?php if(isset($_GET['modif'])) { ?>
            <input type="hidden" name="fieldPK" value="<?=$nameFieldPK?>" />
            <input type="hidden" name="id" value="<?=$_GET['modif']?>" />
            <input type="submit" name="modif_entry" value="Modifier" /> <?php }
        else { ?> <input type="submit" name="add_entry" value="Ajouter" /> <?php } ?>
        </form>
        

    <?php } // END DISPLAY DATA OF THE TABLE

        elseif(isset($_POST['ip']) || isset($_SESSION['ip'])) { // DISPLAY TABLES
        
            if(isset($_POST['ip']))
                extract($_POST);
            if(isset($_SESSION['ip']))
                extract($_SESSION);
        
        $bdd = getTables($ip, $bdd, $user, $pass); ?>

        <table>
            <tr>
                <th>Table</th><th>Accès</th>
            </tr>

            <?php while($tables = $bdd->fetch())
            { ?>

                <tr>
                    <td><?=$tables[0]?></td><td><a href="index.php?table=<?=$tables[0]?>">Voir</a></td>
                </tr>

                <?php 
            } ?>

        </table>

        <a href="index.php?deconnection">Déconnexion</a>

        <?php } // END DISPLAY TABLES
        
        else { // DISPLAY CONNECTION FORM ?>

<form action="index.php" method="post">
    <label for="ip">Adresse IP</label> : <input type="text" name="ip" />
    <label for="bdd">Nom de la Base </label> : <input type="text" name="bdd" />
    <label for="user">Utilisateur</label> : <input type="text" name="user" />
    <label for="pass">Mot de passe</label> : <input type="password" name="pass" />
    <input type="submit" value="Accéder à la BDD"/>
</form>

<?php } // END DISPLAY CONNECTION FORM ?>


    </body>
</html>