<?php 
//creiamo connessione col database tramite PDO
//Argomenti (DNS STRING, user, pass )
/*ALTER TABLE products_crud
MODIFY id INT NOT NULL AUTO_INCREMENT;
ho usato questa query perchè avevo problemi di tipo 1062
*/

$pdo = new PDO("mysql:host=localhost;port=3306;dbname=products_crud", "root", ""); //creo una istanza della classe PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //SE TROVA ERRORE FARà UNA ECCEZIONE

$search = $_GET["search"] ?? ""; //variabile globale per il search
if ($search) { // se il prodotto esiste
  $statement = $pdo->prepare("SELECT * FROM products WHERE title LIKE :title ORDER BY Create_date DESC"); //escimi il prodotto con quel title
  $statement -> bindValue(":title", "%$search%"); //%% necessari per MYSQL
} else { //altrimenti se non ho scritto nulla, dammeli tutti
  $statement = $pdo->prepare("SELECT * FROM products ORDER BY Create_date DESC"); //QUERY CHE SELELZIONA TUTTI I PRODOTTI E LI METTE IN BASE ALLA DATA
}

$statement->execute();
$products = $statement -> fetchAll(PDO::FETCH_ASSOC); //Returns an array containing all of the result set rows 

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
    <h1>Products Crud</h1>

<p>

<a href="create.php" class="btn btn-success">Create Product</a>

</p>

<form> <!-- Barra di ricerca per prodotto -->
  <div class="input-group mb-3">
    <input type="text" class="form-control" 
           placeholder="Search for products"
           name= "search" value="<?php echo $search ?>">
    <button class="btn btn-outline-secondary" type="submit">Search</button>
  </div>
</form>

    <table class="table"> <!--TAVOLE-->
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">image</th>
      <th scope="col">title</th>
      <th scope="col">price</th>
      <th scope="col">create Date</th>
      <th scope="col">action</th> <!--Metteremo edit ed elimina buttons-->
    </tr>
  </thead>
<tbody>
    <?php foreach ($products as $i => $products) : ?> 

      <tr>
        <th scope="row"><?php echo $i + 1 ?></th> <!--Mostriamo Id-->
        <td>
          <img src= "<?php echo $products ["image"]?>" class= "thumb-image"> <!--Mostriamo immagine e diamo una grandezza adeguata-->
        </td> 
        <td><?php echo $products ["title"] ?></td><!--Mostriamo titolo-->
        <td><?php echo $products ["price"] ?></td>
        <td><?php echo $products ["create_date"] ?></td>
        <td>
          <a href="update.php?id=<?php echo $products["id"] ?>" class="btn btn-sm btn-outline-primary">EDIT</a>   
          <form style="display: inline-block" method="post"action="delete.php" >
            <input type="hidden" name="id" value="<?php echo $products["id"] ?>" > 
            <button type="submit" class="btn btn-sm btn-outline-danger">DELETE</button> 
          </form>
       </td> 
    </tr>

    <?php endforeach;  ?>
    </tr>
</tbody>
</table>
    
  </body>
</html>

    