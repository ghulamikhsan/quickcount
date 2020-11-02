<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nama Products - Sonia</title>
<style>
.card {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
  max-width: 300px;
  margin: auto;
  text-align: center;
  font-family: arial;
  padding-bottom: 25px;
}

.price {
  color: grey;
  font-size: 22px;
}

.card button {
  border: none;
  outline: 0;
  padding: 12px;
  color: white;
  background-color: #000;
  text-align: center;
  cursor: pointer;
  width: 100%;
  font-size: 18px;
}

#description {
    margin-bottom: 20px;
}

.card button:hover {
  opacity: 0.7;
}

.card .img {
  background-size: cover;
  width:100%; 
  height:30vh; 
  background-repeat: no-repeat;
  background-position: 50% 50%;
  background-image: url("{{$picts}}");
}
</style>
</head>
<body>

{{-- <h2 style="text-align:center">Sonia App</h2> --}}

<div class="card">
  <div class="img"></div>
  <p class="price">{{$end_price}}</p>
  <p id="description">{{$descriptions}}</p>
</div>

</body>
</html>
