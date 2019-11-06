<?php 

class CommonWidget extends Widget 
{
	public function render($data)
	{
		// dump($data);
		$redirect = $data['redirect'];
		if($redirect){
			return $this->renderFile ("$redirect/index",$data);
		}
	}
}