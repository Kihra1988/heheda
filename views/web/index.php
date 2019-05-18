<?php
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="呵呵买家秀" />
    <meta name="keywords" content="呵呵买家秀" />
    <title>{$webtitle}</title>
    <link rel="stylesheet" href="__ROOT__/Public/Font-Awesome/css/font-awesome.min.css" />
    <link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="/css/style.css?flag=232883"/>
    <script src="/js/jquery-3.3.1.min.js"></script>
    <script src="/js/scroll.js"></script>
    <script src="/js/clipboard.min.js"></script>
    <script src="/js/jquery.qrcode.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/web3.min.js"></script>
</head>
<body class="index-bg">


<!-- 模态框 -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header xg-modal-header">
                <button type="button" class="close xg-close" data-dismiss="modal">&times;</button>
            </div>
<!--            <form  action="upload" enctype="multipart/form-data">-->
<!---->
<!---->
<!--            </form>-->
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'],'action'=>'/web/upload']) ?>

            <?= $form->field($model, 'imageFile')->fileInput() ?>

            <button>Submit</button>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>


<div class="zong">
    <div id="particles-js" class="gradient"></div>
    <div class="container min-h" style="position: relative;">
        <div class="p-t-20 clearfix">
            <div class="pull-left">
                <button class="btn btn-dark btn-hei btn-hei-active" onclick="window.location='/web/index'">所有买家秀</button>
                <button class="btn btn-dark btn-hei btn-hei-active upload_file">上传图片</button>
            </div>

        </div>



        <div class="tab-content">
            <div id="home" class="container tab-pane active"><br>
                <div class="row cards-our">
                   <?php foreach($picInfo as $key=>$val){?>
                       <div class="col-lg-3 col-md-6">
                           <div class="cards">
<!--                               <span class="cards-name">1 <img src="__ROOT__/Public/images/wicon.png"/></span>-->
                               <div class="cards-bg">
                                   <a href='{:U("/Trade/index/platform/$channel/id/")}/{$vo.id}'>
                                       <img src="<?php echo $val['img'] ?>" class="img-fluid" />
                                   </a>
                                   <div class="cards-info">
<!--                                       <div class="clearfix border-b cards-info-s">-->
<!--                                           <span class="float-left"><b>价格：</b>1</span>-->
<!--                                           <span class="float-right"><b>交易：</b>2次</span>-->
<!--                                       </div>-->
                                       <div class="clearfix cards-info-s">
                                           <span class="float-left"><b>作者：</b><span class="text-primary"><?php echo $val['user_address'] ?></span></span>
                                       </div>
                                   </div>
                                   <a href="javascript:void(0);" class='buy2 text-primary' >点赞</a>
                               </div>
                           </div>
                       </div>
                    <?php }?>

                </div>
                <div class="clearfix">
                    <div class='float-right'>
                        <?php
                        echo LinkPager::widget([
                            'pagination' => $pagination,
                        ]);
                        ?>
                    </div>
                </div>
            </div>
            <div id="menu1" class="container tab-pane fade"><br>

            </div>
            <div id="menu2" class="container tab-pane fade"><br>

            </div>

        </div>

    </div>
</div>
<script type="text/javascript">
    /* 弹框居中*/
    function centerModals() {
        var $clone = $(this).clone().css('display', 'block').appendTo('body');
        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
        top = top > 0 ? top : 0;
        $clone.remove();
        $(this).find('.modal-content').css("margin-top", top);
    }
    $('#myModal').on('show.bs.modal', centerModals);
    $('#myModal2').on('show.bs.modal', centerModals);
    $('#myModal3').on('show.bs.modal', centerModals);
    $('#dui').on('show.bs.modal', centerModals);
    $('#cuo').on('show.bs.modal', centerModals);
    $('.upload_file').on('click',function(){
           $('#myModal').modal('show');
    })
</script>

