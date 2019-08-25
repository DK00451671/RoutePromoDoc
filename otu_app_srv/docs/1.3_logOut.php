<?php
<html>
	<head>
		<title></title>
	</head>
	<body>
		<p>
			<font size="18">1.3 logOut</font></p>
		<p>
			<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span></p>
		<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
			<li>
				<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">LOG</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
				<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
					<li>
						<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;logOut&quot;</span>,</span></li>
					<li>
						<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</span></li>
					<li>
						<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span></span></li>
				</ul>
				<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
		</ul>
		<p>
			<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></p>
		<p>
			&nbsp; &nbsp;Test Case: &nbsp; &nbsp; &nbsp;</p>
		<table border="1" cellpadding="1" cellspacing="1" style="width: 700px;">
			<tbody>
				<tr>
					<td style="text-align: center;">
						<span style="font-size:24px;">id</span></td>
					<td style="text-align: center;">
						<span style="font-size:24px;">status</span></td>
					<td style="text-align: center;">
						<span style="font-size:24px;">name</span></td>
					<td style="text-align: center;">
						<span style="font-size:24px;">target</span></td>
					<td style="text-align: center; width: 200px;">
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
						fun</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">LOG</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun1</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;logOut&quot;</span>,</span></li>
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
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;Estructura Json Malformada&quot;}</p>
					</td>
				</tr>
				<tr>
					<td>
						2</td>
					<td style="text-align: center;">
						<span style="background-color: rgb(0, 255, 0);">OK</span></td>
					<td>
						&nbsp;key validation</td>
					<td>
						user</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">LOG</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;logOut&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user1</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span></span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						<p>
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;user name is missing&quot;}</p>
					</td>
				</tr>
				<tr>
					<td>
						2.1</td>
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
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">LOG</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;logOut&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span style="color: rgb(173, 127, 168);">26</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span></span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						<p>
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;invalid user&quot;}</p>
					</td>
				</tr>
				<tr>
					<td>
						<p>
							3</p>
					</td>
					<td style="text-align: center;">
						<span style="background-color: rgb(0, 255, 0);">OK</span></td>
					<td>
						&nbsp;key validation</td>
					<td>
						passwd</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">LOG</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;logOut&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd1</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span></span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						<p>
							{&quot;success&quot;:&quot;false&quot;,&quot;m&quot;:&quot;passwd is missing&quot;}</p>
					</td>
				</tr>
				<tr>
					<td>
						3.1</td>
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
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">LOG</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;logOut&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:</span><span style="color: rgb(173, 127, 168);">26</span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						<p>
							{&quot;success&quot;:&quot;false&quot;,</p>
						<p>
							&quot;m&quot;:&quot;invalid passwd&quot;}</p>
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
							logout</p>
					</td>
					<td>
						&nbsp;</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">LOG</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;logOut&quot;</span>,</span></li>
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
							{&quot;success&quot;:&quot;true&quot;,&quot;m&quot;:&quot;Logout successfully!&quot;}</p>
					</td>
				</tr>
				<tr>
					<td>
						5</td>
					<td style="text-align: center;">
						<span style="background-color: rgb(0, 255, 0);">OK</span></td>
					<td>
						fail logout</td>
					<td>
						&nbsp;</td>
					<td>
						<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span>
						<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
							<li>
								<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">LOG</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
								<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;logOut&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</span></li>
									<li>
										<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;1231&quot;</span></span></li>
								</ul>
								<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
						</ul>
						<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></td>
					<td>
						{&quot;success&quot;:&quot;false&quot;,&quot;m&quot;:&quot;Invalid user name and password&quot;}</td>
				</tr>
			</tbody>
		</table></body>
</html>

?>
