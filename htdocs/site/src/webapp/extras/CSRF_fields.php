<? require_once 'CSRF.php'; ?>
<? $csrf = new CSRF(); ?>
<input type="hidden" name="CSRFname" value="<?= $csrf->name ?>">
<input type="hidden" name="CSRFtoken" value="<?= $csrf->token ?>">