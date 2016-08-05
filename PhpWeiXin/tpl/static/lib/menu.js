var indexdata = 
[
    { text: '客户管理',isexpand:false, children: [ 
		{url:"/index.php?g=Query&m=Consume&a=wxcx",text:"快修录入" },
		{url:"/index.php?g=Query&m=Consume&a=clxx",text:"车辆资料"},
		{url:"/index.php?g=Query&m=Consume&a=membercard",text:"爱养车会员"},
        {url:"/index.php?g=Query&m=Consume&a=acgmembercard",text:"爱车港会员"}
	]
    },
    { text: '消费查询', isexpand: false, children: [
		{ url: "/index.php?g=Query&m=Consume&a=members", text: "爱养车会员消费" },
		{ url: "/index.php?g=Query&m=Consume&a=index", text: "爱车港会员消费" },
	]
    },
    { text: '业绩查询', isexpand: false, children: [
       { url: "/index.php?g=Query&m=Consume&a=purchase", text: "录单员业绩" },
       { url: "/index.php?g=Query&m=Consume&a=fwgw", text: "服务顾问业绩" },
       { url: "/index.php?g=Query&m=Consume&a=persons", text: "个人业绩" } 
    ]
    },
    { text: '配件库存查询', url: "/index.php?g=Query&m=Consume&a=products"},
    { text: '其他查询', isexpand: false, children: [
       { url: "/index.php?g=Query&m=Consume&a=month", text: "每月维修业绩" },
       { url: "/index.php?g=Query&m=Consume&a=days", text: "每日维修业绩" },
       { url: "/index.php?g=Query&m=Consume&a=qcode", text: "二维码生成" } 
    ]
    }
    
];

