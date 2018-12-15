<?php
require_once __DIR__ . '/AbstractBiz.php';


/**
 * App支付请求 业务参数
 *
 * https://doc.open.alipay.com/docs/doc.htm?treeId=204&articleId=105465&docType=1
 */
class AlipayTradeAppPayBiz extends AbstractBiz
{

    public function __construct()
    {
        $this->biz['product_code'] = "QUICK_MSECURITY_PAY";
    }

    public function __get($name)
    {
        return isset($this->biz[ $name ]) ? $this->biz[ $name ] : null;
    }

    public function getBizContent()
    {
        if(!empty($this->biz)){
            $this->bizContent = json_encode($this->biz,JSON_UNESCAPED_UNICODE);
        }
        return $this->bizContent;
    }

    // 订单描述
    public function setBody($body)
    {
        $this->biz['body'] = $body;
    }

    // 商品的标题/交易标题/订单标题/订单关键字等
    public function setSubject($subject)
    {
        $this->biz['subject'] = $subject;
    }

    public function setOutTradeNo($outTradeNo)
    {
        $this->biz['out_trade_no'] = $outTradeNo;
    }

    public function setTimeExpress($timeExpress)
    {
        $this->biz['timeout_express'] = $timeExpress;
    }

    // 订单总金额
    public function setTotalAmount($totalAmount)
    {
        $this->biz['total_amount'] = $totalAmount;
    }

    public function setSellerId($sellerId)
    {
        $this->biz['seller_id'] = $sellerId;
    }

    // 商品主类型：0—虚拟类商品，1—实物类商品
    public function setGoodsType($goods_type) {
        $this->biz['goods_type'] = $goods_type;
    }


    public function setPassbackParams($passback_params) {
        $this->biz['passback_params'] = $passback_params;
    }

    public function setPromoParams($promo_params) {
        $this->biz['promo_params'] = $promo_params;
    }

    public function setExtendParams($extend_params) {
        $this->biz['extend_params'] = $extend_params;
    }

    public function setEnablePayChannels($enable_pay_channels) {
        $this->biz['enable_pay_channels'] = $enable_pay_channels;
    }

    public function setDisablePayChannels($disable_pay_channels) {
        $this->biz['disable_pay_channels'] = $disable_pay_channels;
    }

    public function setStoreId($store_id) {
        $this->biz['store_id'] = $store_id;
    }
}