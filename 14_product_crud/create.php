<?php 
//creiamo connessione col database tramite PDO
//Argomenti (DNS STRING, user, pass )

$pdo = new PDO("mysql:host=localhost;port=3306;dbname=products_crud", "root", ""); //creo una istanza della classe PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //SE TROVA ERRORE FARà UNA ECCEZIONE

$errors = []; //creiamo una array per gli errori, mettero l'allert più in fondo
$title = "";
$price = "";
$description = "";
//image=&title=&description=&price= query string

//ora faremo in modo da prendere le informazioni col metodo post e salvarle nel db (useremo variabili super globali)

if ($_SERVER ["REQUEST_METHOD"] === "POST") {

 $title = $_POST['title']; //prendiamo
 $description = $_POST['description'];
 $price = $_POST['price'];
 $date = date('Y-m-d H:i:s');
  


   if (!$title) { //validiamo 
      $errors[] = "Per favore inserisci il titolo dell'articolo";
   }
   if (!$price) {
     $errors []= "Per favore inserisci il prezzo dell'articolo";
   }
   if (!is_dir("image")){ //se la dir per le immagini non esiste
    mkdir("image"); //allora creala
   }

if (empty($errors)) { // con questa if evitiamo che vengano inseriti prodotti che danno errore
    $image = $_FILES ["image"] ?? null; // qui verranno messi i file image
    $imagePath = "";
    if ($image && $image["tmp_name"]) { //check dei file e correggiamo nel caso non venga data una immagine
      
      $imagePath = "image/".randomString(8)."/". $image["name"]; //assegno ad ogni immagine un path
      mkdir(dirname($imagePath)); //creiamo un folder per le immagini
      move_uploaded_file($image ["tmp_name"], $imagePath); //ora è dentro il programma
    }
  
  $statement = $pdo -> prepare ("INSERT INTO products (title, image, description, price, create_date) 
                    VALUE(:title, :image, :description, :price, :date) ");
  $statement->bindValue(":title", $title); //associo parametro ad una variabile
  $statement->bindValue(":image", "$imagePath");
  $statement->bindValue(":description", $description);
  $statement->bindValue(":price", $price);
  $statement->bindValue(":date", $date);
  $statement->execute();
  header("Location: index.php"); //questo reindirizza all'index dopo aver inserito il prodotto
  }
}

function randomString($n) //creiamo una funzione che assegna ad ogni file una stringa dal nome random, così non si sovrapongono
{
 $characters ="1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
 $str = "";
 for ($i = 0; $i < $n ; $i++) {
  $index = rand(0, strlen($characters) - 1);
  $str .=$characters[$index];
 }
 return $str;
}

?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel = "stylesheet" href="app.css">  
    <title>Products Crud</title>
  </head>
  <body>
  <p>
      <a href= "index.php" class="btn btn-secondary">Go back to products </a>
  </p>

    <h1>Create new Product</h1>
  
  <?php if (!empty ($errors)): ?> <!-- così facciamo in modo che non veda costantemente l'errore dei titoli e prezzi vuoti -->
    <div class ="alert alert-danger"> <!-- L'allert per la validazione -->
       <?php foreach ($errors as $errors): ?>
          <div><?php  echo $errors ?></div>
       <?php endforeach; ?>
    </div>
  <?php endif; ?>

<!-- METTEREMO LE FORMS -->
<!-- in forms specifichiamo 2 attributi:action (dove le forms verranno cedute), metodi -->
<form action="create.php" method="post" enctype="multipart/form-data"> <!-- enctype salverà i files -->
  <div class="mb-3">
    <label >Product image</label>
    <br>
    <input type="file" name="image" >
  </div>
  <div class="mb-3">
    <label >Product title</label>
    <input type="text" name="title" class="form-control" value="<?php echo $title ?>"> <!-- il valore qui serve a non far scomparire subito cià che viene scritto -->
  </div>
  <div class="mb-3">
    <label >Product description</label>
    <textarea class="form-control" name="description"><?php echo $description ?></textarea>
  </div>
  <div class="mb-3">
    <label >Product price</label>
    <input type="number" step=".01" name="price" class="form-control" value="<?php echo $price ?>">
  </div>
  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

  </body>
</html>