<?php 

$pdo = new PDO("mysql:host=localhost;port=3306;dbname=products_crud", "root", ""); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

$id = $_GET["id"] ?? null ; //usiamo la super globale GET

if (!$id) { // se l'id non esiste torneremo ad index
  header("Location: index.php ");
  exit;
}

$statement = $pdo-> prepare("SELECT * FROM products WHERE id = :id"); //facciamo selezionare tutto in base all'id
$statement -> bindValue(":id", $id); //asseganmo paramentro al valore
$statement -> execute();
$products = $statement -> fetch(PDO::FETCH_ASSOC); //il prodotto che voglio


$errors = []; 
$title = $products["title"];
$price = $products["price"];
$description = $products["description"];

if ($_SERVER ["REQUEST_METHOD"] === "POST") {

 $title = $_POST['title']; //prendiamo
 $description = $_POST['description'];
 $price = $_POST['price'];
 

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
    $imagePath = $products["image"]; //dato che siamo negli update qui dobbiamo specificare il path
    
    if ($image && $image["tmp_name"]) { //check dei file e correggiamo nel caso non venga data una immagine
     
      if ($products["image"]){ //così durante l'update l'immagine vecchia sarà eliminata
         unlink($products["image"]);
      } 
      
      $imagePath = "image/".randomString(8)."/". $image["name"]; //assegno ad ogni immagine un path
      mkdir(dirname($imagePath)); //creiamo un folder per le immagini
      move_uploaded_file($image ["tmp_name"], $imagePath); //ora è dentro il programma
    }
  
  $statement = $pdo -> prepare ("UPDATE products SET title = :title, image = :image,
               description = :description, price = :price WHERE id = :id");
  $statement->bindValue(":title", $title); //associo parametro ad una variabile
  $statement->bindValue(":image", "$imagePath");
  $statement->bindValue(":description", $description);
  $statement->bindValue(":price", $price);
  $statement->bindValue(":id", $id);
  $statement->execute();
  header("Location: index.php"); //questo reindirizza all'index dopo aver inserito il prodotto
  }
}

function randomString($n) 
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

    <h1>Update Product <b><?php echo $products["title"] ?></b></h1>
  
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
  
  <?php if ($products["image"]): ?>
    <img src="<?php echo $products["image"] ?>" class="update-image"> <!-- non so perchè ma sta classe non la prende -->
  <?php endif; ?>

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
