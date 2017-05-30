<?php

Function DateCalc($JD)  //jd 转 ut
{
    $JD = $JD +0.5;
    $Z = intval($JD);
    $F = $JD - $Z;
    if ($Z < 2299161)
        $A = $Z;
    else{
        $alpha = floor(($Z - 1867216.25) / 36524.25);
        $A = $Z + 1 + $alpha- floor($alpha / 4);
    }
    $B = $A + 1524;
    $C = floor(($B - 122.1) / 365.25);
    $D = floor(365.25 * $C);
    $E = floor(($B - $D) / 30.6001);
    $Days = $B - $D - floor(30.6001 * $E) + $F;
    if($E < 14) $Months = $E - 1;
    if($E == 14 || $E == 15) $Months = $E - 13;
    if($Months > 2) $Years = $C - 4716;
    if ($Months == 1 || $Months == 2 ) $Years = $C - 4715;
	$DateCalc = $Years . "年" . $Months . "月" . floor($Days) . "日";
	return $DateCalc;
}
Function JDEcalc($year,$month,$day) {
    If ($month == 1 || $month == 2) {
        $year = $year - 1;
        $month = $month + 12;
    }
	$temp=$year.'-'.$month.'-'.floor($day);
    If ( $temp<= '1582-10-04') {
        $B = 0;
    }Else{
        $A = intval($year / 100);
        $B = 2 - $A + intval($A / 4);
    }
    return ( floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $B - 1524.5);
 }
function GetLunar($year,&$month,&$day,&$lunar,$pp=0)
{
	if($pp)
		$jde= JDEcalc($year+1,$month, $day);
	else
		$jde= JDEcalc($year,$month, $day);
	if($month!=12)
	$year--;
	$jq= GetOneYearJQ($year);
	$moon= GetOneYearMoon($year);
	$huyu1=$jq[1];
	$huyu2=$jq[25];

	$cnum=0;
	for($i=1;$i<15;$i++)
	{
		//$moon[$i]+=8/24;
		if($moon[$i]-floor($moon[$i])<0.5) $moon[$i]=floor($moon[$i])-0.5; else $moon[$i]=floor($moon[$i])+0.5;
	}

	if($moon[1]<$huyu1 && $moon[2]<=$huyu1)
	{
		for($i=1;$i<15;$i++)
		{
			if($i==14){$moon[14]=$moon[13]+30;break;}
			$moon[$i]=$moon[$i+1];
		}
	}

	if($moon[1]<$huyu1 && $moon[2]<$huyu1)
	{
		for($i=1;$i<15;$i++)
		{
			if($i==14){$moon[14]=$moon[13]+30;break;}
				$moon[$i]=$moon[$i+1];
		}
	}
	foreach($moon as $tmp)
		if($tmp>$huyu1 && $tmp<=$huyu2) 
		{
			$cnum++; 
			$months[$cnum]=$tmp;
		}
			
	if($cnum==13)
	{
		$lrun=1;
		for($i=2;$i<14;$i++)
		{
			if(!($jq[$i*2-1]>=$months[$i-1] && $jq[$i*2-1]<=$months[$i])) break;
		}
		$run=$i-2;
		if($run<=0) $run+=12;
	}else
		$lrun=0;
	if($year==2033) $run=12;
	for($i=1;$i<15;$i++)
		if($jde>=$moon[$i] && $jde<$moon[$i+1]) break;

	if($i==15 && month==12)
	{
		$res=GetLunar($year-1,$month,$day,$lunar,1);
		return $res;
	}elseif($i==15 && month==11)
	{
		$res=GetLunar($year+1,$month,$day,$lunar,1);
		return $res;
	}
	$startday=$moon[$i];
	//echo $i;
	if($startday-floor($startday)<0.5)
	$startday=floor($startday)-0.5;
	else
	$startday=floor($startday)+0.5;
	$lday=$jde-$startday+1;
	$k=1;
	if($lrun)
	if($i==13 && $run<11)
	{
		$i--; 
		$k=0;
	}
	if($i<3) $i+=12;
	$lmonth=$i-2;
	$p=0;
	if($lrun)
	{

		if($lmonth==$run && $k==1)
		{
			$lmonth--;
			$p=1;
		}elseif($lmonth>$run && ($lmonth <11 && $run<11) && $k)
			$lmonth--;
		elseif($lmonth>$run && $run>11 && $k)
			$lmonth--;
		elseif($lmonth<$run && $run>11 && $k)
		{
			$lmonth--;
			if($lmonth<1) $lmonth+=12;
		}
	}
	$mon=array("零","正月","二月","三月","四月","五月","六月","七月","八月","九月","十月","冬月","腊月");
	$da=array("十","一","二","三","四","五","六","七","八","九","十");
	$tp=floor($lday/10);
	if($lday==10) $tp--;
	switch($tp)
	{
		case 0:
			$tmp="初".$da[$lday%10];
			break;
		case 1:
			$tmp="十".$da[$lday%10];
			break;
		case 2:
			$tmp="廿".$da[$lday%10];
			break;
		case 3:
			$tmp="三".$da[$lday%10];
			break;
	}
	if($lday==20) $tmp="二十";
	$day=$lday;
	$month=$lmonth;
	if($p)
	{
		$result="闰".$mon[$lmonth].$tmp;
		$lunar=1;
	}
	else
	{
		$result=$mon[$lmonth].$tmp;
		$lunar=0;
	}
	return $result;
}

function GetOneYearMoon($year)
{
	$y=floor(($year-1900)*12.36826+12);
	$i=1;
	for ($m=$y;$m<$y+15;$m++)
	{	$M = 1.6 + 29.5306 * $m + 0.4 * sin(1 - 0.45058 * $m);
		$M+=JDECalc(1900,1,1)-1;
		//echo DateCalc($M)."<br />";
		$res[$i]=$M;
		$i++;
	}
	return $res;
}
function GetOneYearJQ($year)
{
	$jq[1]=GetDateJQ($year,0);
	$year++;
	//echo DateCalc($jq[1])."<br />";
	
	for($i=2;$i<26;$i++)
	{
		$p=$i;
		$years=$year;
		if($i>24) {$p=$i-24;}
		$jq[$i]=GetDateJQ($years,$p-1);
		//echo DateCalc($jq[$i])."<br />";
	}
	return $jq;
}
function GetDateJQ($year,$x)
{
	$D=0.2422;
	$Y=$year-2000;
	$L=floor($Y/4);
	$C=array(21.94,5.4055,20.12,3.87,18.73,5.63,20.646,4.81,20.1,5.52,21.04,5.678,21.37,
	  7.108,22.83,7.5,23.13,7.646,23.042,8.318,23.438,7.438,22.36,7.18);
	$day=floor($Y*$D+$C[$x])-$L;
	if($Y==21 && $x==0) $day--;
	if($Y==19 && $x==1) $day--;
	if($Y==82 && $x==2) $day++;
	if($Y==26 && $x==4) $day--;
	if($Y%4==0 && ($x==4 || $x==3)) $day++;
	if($Y==84 && $x==6) $day++;
	if($Y==8 && $x==10) $day++;
	if($Y==16 && $x==13) $day++;
	if($Y==2 && $x==15) $day++;
	if($Y==89 && $x==20) $day++;
	if($Y==89 && $x==21) $day++;
	$yue=array(12,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12);
	$jde=JDECalc($year,$yue[$x],$day);
	//echo $year."-".$yue[$x]."-".$day;
	return $jde;
}
?>