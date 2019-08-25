<?php
<html>
<!--?php
<html-->	<head>
		<title></title>
	</head>
	<body>
		<p>
			<font size="18">2.1 getMyRouLst</font></p>
		<p>
			<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span></p>
		<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
			<li>
				<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">ROU</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
				<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
					<li>
						<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;getMyRouLst&quot;</span>,</span></li>
					<li>
						<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</li>
					<li>
						<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span></li>
				</ul>
				<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
		</ul>
		<p>
			<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></p>
		<p>
			&nbsp;Test Case: &nbsp; &nbsp; &nbsp;</p>
		<table border="1" cellpadding="1" cellspacing="1" style="width: 700px;">
			<tbody>
				<tr>
					<td style="text-align: center;">
						<span style="font-size:24px;">id</span></td>
					<td style="text-align: center;">
						<span style="font-size:24px;">status</span></td>
					<td style="text-align: center;">
						<span style="font-size:24px;">name</span></td>
					<td style="text-align: center; width: 100px;">
						<span style="font-size:24px;">target</span></td>
					<td style="text-align: center; width: 250px;">
						<span style="font-size:24px;">query</span></td>
					<td style="text-align: center; width: 250px;">
						<span style="font-size:24px;">result</span></td>
				</tr>
				<tr>
					<td>
						1</td>
					<td style="text-align: center;">
						<span style="background-color: rgb(0, 255, 0);">OK</span></td>
					<td>
						&nbsp;key validation</td>
					<td>
						<p>
							fun</p>
						<p>
							user</p>
						<p>
							passwd</p>
					</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">ROU</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun1</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;getMyRouLst&quot;</span>,</span></li>
									<li>
										<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user1</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</li>
									<li>
										<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd1</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						<p style="text-align: center;">
							<u><strong>fun&nbsp;</strong></u><span style="background-color: rgb(0, 255, 0);">OK</span></p>
						<p>
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;Estructura Json Malformada&quot;}</p>
						<p style="text-align: center;">
							<u><strong>user&nbsp;</strong></u><span style="background-color: rgb(0, 255, 0);">OK</span></p>
						<p>
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;user name is missing&quot;}</p>
						<p style="text-align: center;">
							<u><strong>passwd&nbsp;</strong></u><span style="background-color: rgb(0, 255, 0);">OK</span></p>
						<p>
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;passwd is missing&quot;}</p>
					</td>
				</tr>
				<tr>
					<td>
						2</td>
					<td style="text-align: center;">
						<span style="background-color: rgb(0, 255, 0);">OK</span></td>
					<td>
						only string</td>
					<td>
						user</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">ROU</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;getMyRouLst&quot;</span>,</span></li>
									<li>
										<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span style="color: rgb(173, 127, 168);">123</span>,</li>
									<li>
										<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						<p>
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;Invalid user&quot;}</p>
					</td>
				</tr>
				<tr>
					<td>
						3</td>
					<td style="text-align: center;">
						<span style="background-color: rgb(0, 255, 0);">OK</span></td>
					<td>
						only string</td>
					<td>
						passwd</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">ROU</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;getMyRouLst&quot;</span>,</span></li>
									<li>
										<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</li>
									<li>
										<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span style="color: rgb(173, 127, 168);">123</span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						<p>
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;Invalid passwd&quot;}</p>
					</td>
				</tr>
				<tr>
					<td>
						4</td>
					<td style="text-align: center;">
						<span style="background-color: rgb(0, 255, 0);">OK</span></td>
					<td>
						<p>
							success</p>
						<p>
							user</p>
					</td>
					<td>
						&nbsp;</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">ROU</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;getMyRouLst&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span></span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						<p>
							<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span></p>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">success</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;true&quot;</span>,</li>
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">m</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">home-office</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;1&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">home1-office</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;2&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">home1-office2</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;3&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">home-officedk</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;12&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">12</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;13&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">home-office111</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;17&quot;</span></span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<p>
							<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></p>
					</td>
				</tr>
				<tr>
					<td>
						5</td>
					<td style="text-align: center;">
						<span style="background-color: rgb(0, 255, 0);">OK</span></td>
					<td>
						<p>
							fail</p>
						<p>
							user</p>
					</td>
					<td>
						&nbsp;</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">ROU</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;getMyRouLst&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;1231&quot;</span></span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						{&quot;success&quot;:&quot;false&quot;,&quot;m&quot;:&quot;Invalid user or password&quot;}</td>
				</tr>
			</tbody>
		</table></body>
</html>

?>
