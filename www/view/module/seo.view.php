<?php
$type_seo = array(
    'nvzhuang' => array(
        '淘宝网女装,新款女装,淘宝商城购物特卖品牌女装',
        '淘宝女装,淘宝网女装,淘宝商城女装,淘宝特卖女装,淘宝网购物女装,淘宝商城新款女装',
        '女装', '女装，新款女装等特卖品牌女装'
    ),
    'nanzhuang' => array(
        '淘宝网商城男装,新款男装,淘宝网购物男装特卖',
        '淘宝男装,淘宝网男装,淘宝商城男装,淘宝时尚男装,淘宝网购物男装',
        '男装', '时尚男装，新款男装等特卖品牌男装'
    ),
    'jujia' => array(
        '淘宝网居家用品,装饰品,淘宝商城家居用品',
        '家具饰品,淘宝特卖,淘宝热卖,淘宝家居,居家用品,居家日用,创意居家',
        '居家用品', '特卖居家用品，居家日用，创意居家等热销产品'
    ),
    'muying' => array(
        '淘宝网婴儿用品,儿童用品,母婴用品网上商城',
        '母婴用品,童装,童鞋,儿童用品,婴儿用品,母婴商城',
        '母婴用品', '母婴用品，婴儿用品，儿童用品，童装童鞋等'
    ),
    'xiebao' => array(
        '淘宝网女鞋,男鞋,女包,包包,淘宝商城时尚鞋包品牌',
        '女鞋,男鞋,鞋子,女包,男包,包包',
        '鞋包', '女鞋，男鞋，女包，男包等时尚品牌鞋包'
    ),
    'peishi' => array(
        '淘宝网饰品,首饰,淘宝商城精美配件',
        '配饰,配件,饰品,皮带,眼镜,首饰,耳环,项链,戒指,胸针,钥匙扣,手机链',
        '配饰', '当季最受欢迎的精美饰品，首饰，配件等时尚配饰'
    ),
    'meishi' => array(
        '淘宝网美食,零食,特产,好吃休闲食品推荐',
        '淘宝网美食,零食,特产,淘宝食品,好吃的零食,休闲零食',
        '美食', '美食，零食，特产等好吃的休闲食品'
    ),
    'shuma' => array(
        '淘宝网数码设备,家用电器,小家电,电脑设备',
        '手机数码,小家电,家用电器,电脑设备',
        '数码家电', '小家电，家用电器，手机数码，电脑设备等数码家电产品'
    ),
    'meizhuang' => array(
        '淘宝网化妆品,护肤品,正品美妆',
        '淘宝网化妆品,护肤品,淘宝美妆',
        '化妆护肤用品', '超值热销的化妆品，护肤品等正品美妆产品'
    ),
    'wenti' => array(
        '淘宝网文化用品,体育用品,户外用品',
        '淘宝网户外,文化用品,体育用品,淘宝户外装备,户外用品',
        '文体用品', '超值热销文化用品，体育用品，户外用品，户外装备'
    ),
    'chepin' => array(
        '淘宝网汽车用品',
        '淘宝网汽车用品,汽车配饰,汽车清洗用品,汽车包养用品,汽车周边配件',
        '汽车用品', '超值热销汽车用品，汽车配饰，汽车清洗用品，汽车包养用品，汽车周边配件'
    ),
);
if (isset($type, $type_seo[$type])) {
    list($title, $keywords, $name, $desc) = $type_seo[$type];
    $title .= ' - 爱折扣';
    $desc   = "汇集独家特约【淘宝网2-5折{$name}】，天天有新款，价格足够低，先到先得。为您精选淘宝网及淘宝商城{$desc}。";
} else {
    $title = '【爱折扣官网】爱生活，爱折扣 - 最优质的商品，最给力的折扣价';
    $keywords = '爱折扣,izheko,爱折扣网,淘宝折扣,天猫折扣,限时折扣,优质折扣,折扣商品,九块九,九块九包邮,9.9包邮,九块邮';
    $desc = '爱折扣精选淘宝、 天猫的优质折扣商品。最优质的商品，最给力的折扣价，还包邮，先到先得哦。【9.9包邮，天天有】';
}
?>
        <title><?= $title ?></title>
        <meta name="keywords" content="<?= $keywords ?>" />
        <meta name="description" content="<?= $desc ?>" />