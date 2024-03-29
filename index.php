<?php
session_start();
try {
    require_once 'db.php';

    // a new products collection object
    $collection = $db->products;


    if (isset($_GET["search_cat"]) && isset($_GET["keyword"])) {
        $keyword = $_GET["keyword"];
        $value = $_GET["search_cat"];
        $query = array($value => array('$regex' => new MongoRegex("/$keyword/i")));
        $cursor = $collection->find($query);
    } else {
        // fetch all product documents
        $cursor = $collection->find();
    }

    // How many results found
    $num_docs = $cursor->count();


} catch (MongoConnectionException $e) {
    // if there was an error, we catch and display the problem here
    echo $e->getMessage();
} catch (MongoException $e) {
    echo $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Soko Nyeusi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="resources/css/style.css">
    <script src="resources/js/script.js"></script>
    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type='hidden' name='business' value='Paypal_Business_TestAccount_Id'>
    <input type='hidden' name='item_name' value='Mobile'>
    <input type='hidden' name='item_number' value='CAM#N1'>
    <input type='hidden' name='amount' value='0.01'>
    <input type='hidden' name='no_shipping' value='1'>
    <input type='hidden' name='currency_code' value='USD'>
    <input type='hidden' name='notify_url' value='http://SITE NAME/payment.php'>
    <input type='hidden' name='cancel_return' value='http://SITE NAME/cancel.php'>
    <input type='hidden' name='return' value='http://SITE NAME/success.php'>
    <!-- COPY and PASTE Your Button Code -->
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="### COPY FROM BUTTON CODE ###">
    <input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynow_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    </form>
</head>

<body>
<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Soko Nyeusi</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Home</a></li>

                <?php if (isset($_SESSION["account"])) { ?>
                    <li class="active"><a href="account.php">Orders</a></li>
                <?php } else { ?>
                    <li class="active"><a href="account.php">Account</a></li>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">

                <?php if (isset($_SESSION["account"])) { ?>
                    <li><a href="account.php?logout"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">

    <div class="well well-sm">
        <strong>Display</strong>
        <div class="btn-group">
            <a href="#" id="list" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th-list">
            </span>List</a> <a href="#" id="grid" class="btn btn-default btn-sm"><span
                    class="glyphicon glyphicon-th"></span>Grid</a>
        </div>
        <a id="addToCart" class="btn btn-default btn-md" href="cart.php"><span
                class="glyphicon glyphicon-shopping-cart btn-md"
                id="basket"></span> products in
            basket</a>
    </div>

    <?php if (!isset($_GET["id"])) { ?>
        <div class="row">
            <form action="index.php" method="get">
                <div class="form-group pull-right">
                    <label for="sel1">Choose category</label>
                    <select name="search_cat" class="form-control" id="sel1">
                        <option value="title">Product name</option>
                        <option value="description">Product description</option>
                    </select>
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Search for...">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Go!</button>
      </span>
                    </div><!-- /input-group -->
                </div>

            </form>
        </div>
    <?php } else { ?>
        <a onclick="window.history.back();" href="#" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-arrow-left"></span> Back
        </a>
    <?php } ?>

    <?php if (!isset($_GET["id"])) { ?>
        <div id="products" class="row list-group">
            <?php
            if ($num_docs > 0) {
                // loop over the results
                foreach ($cursor as $obj) {
                    ?>
                    <div class="item  col-xs-4 col-lg-4">
                        <div class="thumbnail">
                            <img class="group list-group-image" src="uploads/<?php echo $obj['image']; ?>" alt=""/>
                            <div class="caption">
                                <h4 class="group inner list-group-item-heading"><a
                                        href="index.php?id=<?php echo $obj['_id']; ?>"><?php echo $obj['title']; ?></a>
                                </h4>
                                <p id="description" class="group inner list-group-item-text">
                                    <?php echo $obj['description']; ?></p>
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <p class="lead">
                                            $ <?php echo $obj['price']; ?></p>
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <a class="btn btn-success"
                                           onclick=addtocart('<?php echo $obj['_id'] . "','" . urlencode($obj['title']) . "','" . $obj["price"]; ?>')>Add
                                            to cart</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php
                }
            } else {
                // if no products are found, we show this message
                echo "No products found \n";
            }


            ?>
        </div>

    <?php } else {

        $collection = new MongoCollection($db, 'products');
        $sweetQuery = $collection->findOne(array('_id' => new MongoId($_GET["id"])));

        $cursor = $collection->find($sweetQuery);
        foreach ($cursor as $obj) {
            ?>
            <div class="item">
                <div class="thumbnail">
                    <img class="group list-group-image" src="uploads/<?php echo $obj['image']; ?>" alt=""/>
                    <div class="caption">
                        <h4 class="group inner list-group-item-heading"><a
                                href="index.php?id=<?php echo $obj['_id']; ?>"><?php echo $obj['title']; ?></a>
                        </h4>
                        <p class="group inner list-group-item-text">
                            <?php echo $obj['description']; ?></p>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <p class="lead">
                                    $ <?php echo $obj['price']; ?></p>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <a class="btn btn-success"
                                   onclick=addtocart('<?php echo $obj['_id'] . "','" . urlencode($obj['title']) . "','" . $obj["price"]; ?>')>Add
                                    to cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <?php


        }
    }

    // close the connection to MongoDB
    $conn->close();
    ?>
</div>

</body>
</html>