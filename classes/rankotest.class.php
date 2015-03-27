<?php

class rankotest {
	
	public function parse_xls($xls_file)
	{
		if (file_exists($xls_file)) 
		{				
			if(pathinfo($xls_file,PATHINFO_EXTENSION) != "xlsx")
			{
				die('Błąd: niedozwolony typ pliku');
			}
			
			$objReader = new PHPExcel_Reader_Excel2007();
			$objPHPExcel = $objReader->load($xls_file);								
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			
			if($sheetData)
			{
				if($sheetData[1]['A']!='Poziom 1' || $sheetData[1]['B']!='Poziom 2' || $sheetData[1]['C']!='Poziom 3' || $sheetData[1]['D']!='ID')
				{
					die('Błąd: niezgodne kolumny pliku XLS');
				}
				
				array_shift($sheetData);			
				$result = $this->reindex_array($this->generate_tree($sheetData));		
				return json_encode($result);	
			}
			else
			{
				die('Błąd: nie można przetworzyć pliku XLS');
			}		
		}
		else
		{
			die('Błąd: brak pliku XLS');
		}
	}

	private function generate_tree($arr)
	{
		$tree = array();
		$lastvalues = array();
		
		foreach($arr as $row_id => $row)
		{
			$id = $row['D'];
			$row_arr = array_values($row);
					
			for($i=0;$i<3;$i++)
			{
				if($row_arr[$i])
				{
					$lastvalues[$i] = $row_id;
					
					if($i)
					{				
						if($i==1)
						{
						$tree[$lastvalues[$i-1]]['nodes'][$row_id]=array('id'=>$id,'value'=>$row_arr[$i]);							
						}
						elseif($i==2)
						{
						$tree[$lastvalues[$i-2]]['nodes'][$lastvalues[$i-1]]['nodes'][$row_id]=array('id'=>$id,'value'=>$row_arr[$i]);								
						}					
						// można dodać obsługę kolejnych poziomów zagłębień...				
					}
					else
					{
						$tree[$row_id]=array('id'=>$id,'value'=>$row_arr[$i]);
					}				
				}
			}
		}
		return $tree;
	}

	private function reindex_array($arr)
	{
		$finalArray = array();
		foreach ($arr as $key=>$val ){		
			if(is_array($val['nodes']))
			{
			$val['nodes']=$this->reindex_array($val['nodes']);
			}
			$finalArray[] = $val;		    
		}
		return $finalArray;
	}
}
?>