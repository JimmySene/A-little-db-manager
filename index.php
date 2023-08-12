<?php session_start();

include('function.php'); ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            A little DB Manager
        </title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>
        <header>
            <h1 class="title is-1 has-text-centered">A little DB Manager</h1>
            
        </header>
        
        <div id="content" class="container is-fluid">

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
        
        elseif(isset($_GET['table']) && isset($_SESSION['bdd'])) { // DISPLAY FIELDS AND DATA OF THE TABLE 
            $table = htmlspecialchars($_GET['table']); 
            $primaryKey = 0; $i = 0; $nameFieldPK = ""; ?>
            
      
            <h2 class="title has-text-centered">Table <?=$table?> de <?=$_SESSION['bdd']?></h2>

            <?php if(isset($_GET["error"])) { ?>
                <p class="block has-text-centered error"><b>Erreur :</b> Une ou plusieurs données ne correpondent pas aux types attendus par la table. Veuillez réessayer.</p>
            <?php } ?>
           
            <p class="block has-text-centered"><a href="index.php?table=<?=$table?>#footer">Aller tout en bas</a></p>

        <table class="table is-bordered is-striped is-fullwidth">
                <tr >
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
                        <td><a href="index.php?modif=<?=$id[$j]?>&table=<?=$table?>#form_entry"><i class="fas fa-edit"></i></a></td>
                        <td><a href="index.php?sup=<?=$id[$j]?>&table=<?=$table?>&field_pk=<?=$nameFieldPK?>" class="icon"><i class="fas fa-trash"></i></a></td>
                </tr> 
                <?php 
            $j++; } ?>
        
            </table>

            <p class="block has-text-centered"><a href="index.php">Voir les tables (retour)</a></p>

            <?php if(isset($_GET['modif'])) // DISPLAY INFORMATIONS IN FIELDS IF MODIFICATION
                    $theEntry = getEntry($table, $_GET['modif'], $nameFieldPK); ?>
            <hr />
            <div class="columns">
                <div class="column is-5 is-offset-3">
                    <form action="index.php" method="post" id="form_entry">
                    
                        <?php
                        foreach($fields as $field) { 
                                if($field['Key'] != 'PRI') { // CHECK IF FIELD IS NOT PRIMARY KEY 
                                $nameField = $field['Field']?>
                                <div class="field is-horizontal">
                                    <div class="field-label">
                                        <label class="label" for="<?=$nameField?>"><?=$nameField?></label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <div class="control">
                                                    <input type="<?php if(in_array($field["Type"], ["tinyint", "smallint", "mediumint", "int", "bigint", "float", "double"])) echo 'number';
                                                    else echo 'text'; ?>" 
                                                    placeholder="<?php if($field["Type"] === "date") echo 'YYYY-MM-JJ';
                                                    else if($field["Type"] === "datetime") echo 'YYYY-MM-JJ hh:mm:ss'?>"
                                                    <?php if($field["Null"] === "NO") echo "required"; ?>
                                                    class="input" name="<?=$nameField?>" id="<?=$nameField?>"
                                                value="<?php if(isset($_GET['modif'])) { echo $theEntry[$nameField]; } ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                        <input type="hidden" name="table" value="<?=$table?>" />
                    <?php if(isset($_GET['modif'])) { // 2 HIDDEN FIELDS IF MODIFICATION AND MODIF BUTTON ?>
                        <input type="hidden" name="fieldPK" value="<?=$nameFieldPK?>" />
                        <input type="hidden" name="id" value="<?=$_GET['modif']?>" />
                        
                        <div class="field is-horizontal">
                            <div class="field-label">
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <input type="submit" name="modif_entry" value="Modifier" class="button is-fullwidth is-link" /> 
                                    </div>
                                </div>
                            </div>
                        </div><?php }
                    else { // ADD BUTTON ?> 
                    <div class="field is-horizontal">
                            <div class="field-label">
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <input type="submit" name="add_entry" value="Ajouter" class="button is-fullwidth is-link" /> 
                                    </div>
                                </div>
                            </div>
                        </div>
                     <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
            

        <?php } // END DISPLAY DATA OF THE TABLE

            elseif(isset($_POST['ip']) || isset($_SESSION['ip'])) { // DISPLAY TABLES
            
                if(isset($_POST['ip']))
                    extract($_POST);
                elseif(isset($_SESSION['ip']))
                    extract($_SESSION); 
                    
                    $bdd_tables = getTables($ip, $bdd, $user, $pass); 
                    if($bdd_tables == null)
                        header('location:index.php'); ?>

            <div class="columns">
                    <div class="column is-4 is-offset-4">
                        <h2 class="title has-text-centered">Choisir une table de <?= $bdd ?></h2>
                         
                        <table class="table is-bordered is-striped is-fullwidth">
                            <tr>
                                <th>Table</th><th>Accès</th>
                            </tr>

                            <?php while($tables = $bdd_tables->fetch())
                            { ?>

                                <tr>
                                    <td><?=$tables[0]?></td><td><a href="index.php?table=<?=$tables[0]?>">Voir</a></td>
                                </tr>

                                <?php 
                            } ?>

                        </table>
                
                        <p class="has-text-centered"><a class="button is-danger" href="index.php?deconnection">Déconnexion</a></p>
                    </div>
                </div>

            <?php } // END DISPLAY TABLES
            
            else { // DISPLAY CONNECTION FORM ?>
                <div class="columns">
                    <div class="column is-half is-offset-one-quarter">
                        <h2 class="subtitle is-3 has-text-centered">Se connecter à une BDD</h2>
                        <form action="index.php" method="post">
                            <div class="field">
                                <label for="ip" class="label">Adresse IP</label>
                                <div class="control">
                                    <input type="text" name="ip" class="input" value="127.0.0.1" placeholder="127.0.0.1" required onclick="this.value = ''" />
                                </div>
                            </div>
                            <div class="field">
                                <label for="bdd" class="label">Nom de la Base </label>
                                <div class="control">
                                    <input type="text" name="bdd" class="input" required />
                                </div>
                            </div>
                            <div class="field">
                                <label for="user" class="label">Utilisateur</label>
                                <div class="control">
                                    <input type="text" name="user" class="input" required />
                                </div>
                            </div>
                            <div class="field">
                                <label for="pass" class="label">Mot de passe</label>
                                <div class="control">
                                    <input type="password" name="pass" class="input" required />
                                </div>
                            </div>
                            <div class="field">
                                <div class="control">
                                    <input type="submit" value="Accéder à la BDD" class="button is-fullwidth is-link" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            <?php } // END DISPLAY CONNECTION FORM ?>

        </div>
        <footer id="footer" class="footer">
            <div class="content has-text-centered">
                <p>&copy; A little DB Manager ; 2018 - 2023 ; créé par par <a href="https://github.com/JimmySene/">J.S</a></p>
                <p><i class="fab fa-github"></i> <a href="https://github.com/JimmySene/a-little-db-manager">GitHub</a></p>
                <p><a href="https://bulma.io"><img src="https://bulma.io/images/made-with-bulma.png" alt="Made with Bulma" width="128" height="24"></a></p>
            </div>
        </footer>
    </body>
</html>