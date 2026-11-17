<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · includes/pie.php
 * ============================================================================
 *  Pie común de todas las páginas: cierra el contenido principal, muestra el
 *  footer y carga como módulos ES los scripts JavaScript que pida la página.
 */
$scripts = $scripts ?? [];
?>
</main>
<footer><p>AulaFix · Proyecto</p></footer>
<?php foreach($scripts as $src): ?>
    <script type="module" src="<?=e($src)?>"></script>
<?php endforeach; ?>    
</body>
</html>