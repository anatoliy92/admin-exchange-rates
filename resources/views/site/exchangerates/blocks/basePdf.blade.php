<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Nationalbank</title>
	<style type="text/css">
			@page { margin: 20px; }
			body {
				position: relative;
				font-family: 'DejaVu Sans', Times, serif;
				/* border: 1px solid; */
			}
			p {
					margin-bottom: 0.25cm;
					direction: ltr;
					line-height: 120%;
					text-align: left;
					orphans: 2;
					widows: 2;
					font-size: 10px;
					word-wrap: break-word;
			}

			.header {
				position: fixed;
				top: 12px; right: 0;
				width: 100%;
				text-align: right;
				font-size: 12px;
			}
			hr {
				display: block;
				height: 10px;
				width: 100%;
				background: #0E4C28;
				margin: 0;
			}
			.footer {
				position: fixed;
				bottom: 85px;
				width: 100%;
				text-align: right;
				font-size: 10pt;
				line-height: 8pt;
				/* border: 1px solid red; */
			}
			.footer p {
				text-align: left;
				font-size: 10px;
				line-height: 10px;
			}
			.footer span:before {
				content: counter(page);
			}
			.description {
				text-align: center;
				font-size: 12px;
				margin-bottom: 10px;
			}
			table td {
				border: 1px solid #CCCCCC;
				font-size: 14px;
				text-align: center;
				padding: 10px;
			}

			table thead td {
				border: 1px solid #CCCCCC;
				padding: 10px;
			}

			table thead {
				font-weight: 700;
				background: #F4F4F4;
				text-align:center;
			}
	</style>
</head>
<body>
	<div class="header">{{ env('APP_URL') }}</div>

	<div class="logo">
		<img src="{{ public_path('/site/logo_rus.jpg') }}" >
	</div>

	<hr>

	<div class="description">
		Официальные (рыночные) курсы валют на <br/> {{ $request['beginDate'] }} - {{ $request['endDate'] }}
	</div>

	<div class="body" style="overflow: hidden;">
		@include($template)
	</div>

	<div class="footer">
		<p>В соответствии с Законом Республики Казахстан «О средствах массовой информации» информация о курсах иностранных валют поотношению к тенге,
			представленная на официальном интернет-ресурсе Национального Банка Республики Казахстан {{ env('APP_URL') }}, является официальным сообщением Национального
			Банка Республики Казахстан и не требует дополнительногописьменного подтверждения от Национального Банка Республики Казахстан."</p>
		<span></span>
	</div>
</body>
</html>
