<div style="width: 642px;border-radius: 3px;">
	<div style="background-color: #289be5;height: 53px;border-top-left-radius: 3px;border-top-right-radius: 3px;font-family: ArialNarrow, Arial;color:  #ffffff;font-size: 18px;font-weight: 400;line-height: 53px;text-align: left;padding: 0 30px;">Сообщение отправлено с сайта <?php echo str_replace('www.', '', $_SERVER['HTTP_HOST']);?></div>
	<div style="padding: 20px;background-color:  #f2f5f7;">
		<?php foreach($mailMessage as $strMessage):?>
			<div style="padding: 15px;border-radius: 1px;background-color:  #ffffff;box-shadow: 0px 1px 1px 0px rgba(225, 230, 234, 0.75);min-height: 16px;margin-bottom:5px;font-family: ArialNarrow, Arial;">
				<div style="width:50%;float:left;">
					<?php if (isset($strMessage["name"])) echo $strMessage["name"];?>:
				</div>
				<div style="width:50%;float:left;color:  #666f78;font-size: 14px;font-weight: 400;">
					<?php echo $strMessage["message"];?>
				</div>
				<div style="clear:both"></div>
			</div>
		<?php endforeach;?>
	</div>
</div>