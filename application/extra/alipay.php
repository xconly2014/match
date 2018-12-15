<?php
/**
 * 支付宝支付
 */

return [
    //应用ID,您的APPID。
    'app_id' => "2017120600401538",

    //异步通知地址
    'notify_url' => "http://api.weplay.cmswat.com/notify/ali",

    //同步跳转
    'return_url' => "",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqwoqh58aRaDWsMwsBEVqM/d/ExPAzU4pWNClOzXVse/A8LdPSFm8YE4SNo1wxHr6HNmBgJkj1gIGmW1hzpeqh6YpAHWUeWFqZ95u3DXN140ODhrnyCJrD9vDJ48oOzIpgG6WK9MVgY6zxtTIQGq6ryO2czSbDc5H1nuTqXXD1EghLGCAb2xj3L2w5hC7P25/EfD46rFHX5E8m197AT9OoZnPYWIwC409Kgg56d9UkrW4zxWOLa0I2smxuZoQlQOD7SAiTiXS5UvDilwPTxKHUh3nhz4TvV4uuohHWP+D3hyWrFo8nuHjcA3UWwAqjclrC53sSf+3abWE7Ur6DV7RewIDAQAB",

    //商户私钥, 请把生成的私钥文件中字符串拷贝在此
    'rsa_private_key' => "MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCD+Y5wSfU6VfvL4Us77u4Xpf6sLjxrvwbrXrxPHlNiJPy3DUXinmhnbUSNOXKRGyPHUdw+VhdJK65NEr/fD6fHpFdLDW8N1pnkCfljHFU3m+MM7+Goeh+Ew0Y1PlAElCRpIzXcgwVyxStdoDq2pk+jJIWl0TBq4vWvWSDEbR+eoPnOSAFpr4+BkLlvCrEe8suJ+/U/8GTpycIcWwZjSD4fAP66JfABIFwOkqozO4NZcvPqf+e1m4qpTdjnKJAbEq6qpi9wX/EpiqNWZ1jCRXZcdl9oDWbh5eVmvmJ2H86CldXIumyZWIz17sJ35uJuGYItw2jtUJ7a3U5/DCkWeaQVAgMBAAECggEAfjYkPUjNZ+ug8TrVK0z1j9Ikut9A0z4NzPx9lCCJV7vNnvXnlrEtYXQC8hg1c4YD8nNb8JV+kXBfsb+zlQwwxWFqRhFj2lUYORg80sbezF6dhdrG8MtR5zKkqvibp7VRGOcedHMsu7TKIzC/1bs5P+kGkyx6ZE4G7ZmMF63QZm9llbhIeL+2BJ6iOfOUW75oDjKV52TmXW+h1xBDHjDAQJDqPB559ZII950Y7KumXGXCM4uwGvEMmTkZpF3JKIGd3S/wiLfTeZY2qLONHbTs0DQazENUHmnGofori52Yq2fhRJzkvivVNk8MJ74hH6+auWIdXdMsqWXmxzY5jr+zxQKBgQDHi7KAipaxMK7z5My5Ts5OoLT9PMCqXt+IEpvNvr0rvFd9ZICm9dC7g8FeRUL3FxlKkrSy9Wbt/rxyCSE/XlcCg6AAcY+fJAdi+2QjVQofmnNcTfhafZlkFTI24y3d3d+0K/plULQdAWZu8dfaXTuldpEMhg9JjO65flFOB1nRHwKBgQCpT/g7CJIwZu1/wcbNv7gutsG4xW/TGxeE8SlbnwBDqQfB3+WkMfJrVYaoPNEj0AfvY0vd/O8lzvd85Livdg4C/Kn4u7xPMHJinf+efiuZVOWm+zG5fQwyleQpbSiAHRoJdlzbnph0qw/7GyHBdPCG5iQp/e2u92b7Y9/e9sqgSwKBgA50HPLYlv+woguCeB24CpOBanqpSt9MTe6iQmzNsxAW7VNk0zjEt2DNs1TruWUCZE+VfsczpUf52pZ7+M+JOs4w0IcXsqp5Rpuxz9gLUddmLZLzBtkvHcw1CNUW8XyPlKONR+7MwK8VKeFBXTww4qAijXEVyMewEJ6Y+rSnLadLAoGAQ/KY/zAAYzZbZ+MfYqDnTLABbKKnrwP3W13hKCjWVVD3kQHGyi9VR2gKY2gM0p+BH7wszUjdvMGjptavm5L4KpC1seG9v9YEgblAxZ2C58UfaPT0Tql1hLOM3mc1UrzbHyMGPwkVy/R+tFp+IIJKqQihSMzkX0xgYtEBHhzS0qMCgYBFuhY3iosd0gdJTQVzEE8qXNtJ9akMO89icBlQN4hWXAikH9BNwTY/rEB7nsw+PQ4/LlEO+uP2khPSJ1B5W07vbDcx0Kr1zbzmC44CHitBqIPGxsJz9zEvEmCtT6wtLbcZ8OJ0z5CAX2FXtZd81x4lBk85/R+zC2aSD7MylNZ8Ng==",

    'format' => "json"
];
