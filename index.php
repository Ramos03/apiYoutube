<html>
    <head>
        <title>API Youtube</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous">
    </head>

    <body>
        <form method="GET" action="integraYoutube.php">
        <div class="col-md-4 mb-3">
            <label>Texto a ser procurado</label>
            <input type="text" class="form-control" id="q" name="q" required>

            <label for="validationTooltip02">Quantidade</label>
            <input type="number" class="form-control" id="maxResults" name='maxResults'  min="1" max="10" step="1" value="10" required>
            
            <br>
            <button type="submit" class="btn btn-primary">Procurar</button>
        </div> 
        </form>
    </body>
</html>