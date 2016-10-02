<?php

namespace TzlPHP;
class Page
{
	/*********************** 制作翻页 **********************/
	// 参数1：总的记录数
	// 参数2：每页显示条数【如果不传，默认10条】
	public static function makePage($count, $perPage = 10)
	{
		// 1. 计算总的页数
		$pageCount = ceil($count / $perPage);   // ceil：向上取整

		// 2. 接收当前是第几页的变量【必须是一个大于1的整数】
		$p = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;

		// 3. 限制当前页不能超过总的页数【只有在数据时才需要限制:$pageCount > 0】
		if($pageCount > 0 && $p > $pageCount)
			$p = $pageCount;

		// 4. 计算limit的第一个参数【从第几条记录开始取】
		$offset = ($p-1) * $perPage;  // （当前页-1）*第页显示的条数

		// 5. --------------------------------->   拼出翻页的字符串     <---------------------------------
		// 拼翻页字符串
		$pageString = '<div class="page">';
		// 当有记录才拼出翻页的按钮
		if($pageCount > 0)
		{
			/******************** 获取当前所有URL后面的参数 **********************/
			// 把原来的get参数拼成一个URL形式的字符串
			$oldPara = '';
			$oldParaHidden = '';
			// 如果不为空说明有参数就拼一个URL字符串
			if($_GET)
			{
				// 把原来的p删除
				unset($_GET['p']);
				foreach($_GET as $k => $v)
				{
					// 拼出字符串放到翻页的按钮上
					$oldPara .= "$k=$v&";
					// 还要拼出隐藏域放到跳转的表单中
					$oldParaHidden .= "<input type='hidden' name='$k' value='$v'>";
				}
			}
			// 如果大于第一页才有上一页
			if($p > 1)
				$pageString .= '<a href="index.php?'.$oldPara.'p='.($p-1).'">上一页</a>';

			/***** 仿京东，只显示7个页码按钮 ********/
			// 如果当前页小于5页就显示连续的前几页的页码
			if($p<=5)
			{
				$start = 1;
				// 如果总页数大于7那么就到7，如果总的页数小于7就到总的页数
				$end = min(7, $pageCount);
			}
			else if($p >= 6 && $p <= $pageCount - 3)
			{
				// 加上1，2页和三个点
				$pageString .= '<a href="index.php?'.$oldPara.'p=1">1</a>';
				$pageString .= '<a href="index.php?'.$oldPara.'p=2">2</a>';
				$pageString .= ' ... ';

				$start = $p-2;
				$end = $p + 2;
			}
			else
			{
				// 加上1，2页和三个点
				$pageString .= '<a href="index.php?'.$oldPara.'p=1">1</a>';
				$pageString .= '<a href="index.php?'.$oldPara.'p=2">2</a>';
				// 总页数大于7时需要加前三点
				if($pageCount > 7)
					$pageString .= ' ... ';

				$start = $pageCount - 4;
				$end = $pageCount;
			}

			// 从第1页开始循环制作按钮
			for($i=$start; $i<=$end; $i++)
			{
				// 如果是当前页是加个active样式
				if($i == $p)
					$pageString .= "<a class='active' href='index.php?{$oldPara}p={$i}'>{$i}</a>";
				else
					$pageString .= "<a href='index.php?{$oldPara}p={$i}'>{$i}</a>";
			}

			// 当前最后一个页码如果小于总的页数
			if($end < $pageCount)
			{
				$pageString .= ' ... ';
			}

			// 如果当前页小于最后一页
			if($p < $pageCount)
				$pageString .= '<a href="index.php?'.$oldPara.'p='.($p+1).'">下一页</a>';

			$pageString .= "共{$pageCount}页，跳转<form style='display:inline;' action='index.php'>{$oldParaHidden}<input value='{$p}' type='text' size='3' name='p'>页<input type='submit' value='跳转'></form>";
		}
		else
		{
			$pageString .= '当前没有记录！';
		}
		$pageString .= '</div>';

		// 7. 返回制作好的翻面数据
		return [
			'pageString' => $pageString,   // 熜页字符串
			'limit' => " $offset,$perPage",  // 返回LIMIT参数
		];
	}
}