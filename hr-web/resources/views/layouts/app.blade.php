<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <link rel="icon" href="{{ asset('sphinx1.ico') }}" type="image/x-icon">
</head>
<style>
     @import url('https://fonts.googleapis.com/css2?family=Sora:wght@100..800&display=swap');
.gradient-text {
      background: linear-gradient(to bottom, rgb(255, 88, 90), rgb(168, 27, 189));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
  }

.gradient-btn{
  background: linear-gradient(to right, rgb(255, 88, 90), rgb(168, 27, 189));
}
.gradient-btn2{
    background: linear-gradient(to right, rgb(64, 156, 255), rgb(0, 242, 254));
}
.gradient-btn3 {
    background: linear-gradient(to right, rgb(255, 64, 64), rgb(255, 0, 0));
}
.gradient-bg {
    background: linear-gradient(to right, rgb(255, 88, 90), rgb(168, 27, 189));
}

</style>
<body class="font-sora">
@include('layouts.partials.navbar')
<div class="conatainer mx-auto px-12">
@yield('content')

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>

</div>
</html>
