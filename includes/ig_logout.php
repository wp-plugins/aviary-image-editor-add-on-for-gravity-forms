<head>
  <script type='text/javascript' src='js/jquery.js'></script>
  <script type="text/javascript">
  var errloading;

  $(document).ready(function () {
      $('#DtLoadingIframe').one('load', (function () {
          clearTimeout(errloading);
      }));

      errloading = setTimeout("RedirectApp()", 3000);
  });

  function RedirectApp() {
      window.close();
  }

  </script>
</head>
<body>
  <iframe src="https://instagram.com/accounts/logout/" width="0" height="0" />
</body>
