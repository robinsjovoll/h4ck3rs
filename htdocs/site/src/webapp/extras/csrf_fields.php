
/**
 * Created by IntelliJ IDEA.
 * User: Robin
 * Date: 05.04.2016
 * Time: 18:58
 */

<? require_once 'csrf.php'; ?>
2	<? $csrf = new CSRF(); ?>
3	<input type="hidden" name="CSRFname" value="<?= $csrf->name ?>">
4	<input type="hidden" name="CSRFtoken" value="<?= $csrf->token ?>">