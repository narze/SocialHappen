<script type="text/javascript" id="vars">
  <?php foreach($vars as $name => $value) {
    if(!is_int($value)) {
      $value = "'{$value}'";
    }
    echo "var {$name} = {$value};\n";
  }?>
</script>