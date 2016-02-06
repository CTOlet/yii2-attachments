<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 2/6/2016
 * Time: 9:59 PM
 */
/* @var $this \yii\web\View */
/* @var $content string */
?>

<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody(); ?>
    </body>
    </html>
<?php $this->endPage() ?>