<?php
/*-----------------------------------------------------+
 * 工具类
 *
 * 
 +-----------------------------------------------------*/

class Utils {
    /**
     * 生成数据分页索引
     * @param int $totalRecord 总记录数
     * @param int $currentPage 当前页
     * @param int $limit 每页显示的记录数
     * @param int $half 往左右廷伸的索引个数,默认为5个
     * @return string html字串
     */
    public static function pager($totalRecord, $currentPage, $limit, $half = 5) {
        if($limit > 0){
            $totalPage = $limit > $totalRecord ? 1 : $totalRecord % $limit ? ( int ) ($totalRecord / $limit) + 1 : ( int ) ($totalRecord / $limit);
        }else{
            $totalPage = 1;
        }
        $currentPage = ($currentPage > 0 && $currentPage < $totalPage) ? ( int ) $currentPage : 0;
        if ($totalPage > $half * 2 && ($currentPage > $half)) {
            if (($currentPage + $half) < $totalPage) {
                $j = $currentPage + $half + 1;
                $i = $currentPage - $half;
            } else {
                $j = $totalPage;
                $i = $currentPage - ($half * 2 - ($j - $currentPage));
            }
        } else {
            $i = 0;
            $j = $totalPage > $half * 2 + 1 ? $half * 2 + 1 : $totalPage;
        }

        $html = '<div class="row"> <div class="col-sm-5"> <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">当前显示 '. ($currentPage+1) .'/'. $totalPage.'</div> </div>';
        $html .= '<div class="col-sm-7"> <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate"> <ul class="pagination">';
        $disabled = $currentPage == 0 ? "disabled" : '';

        // previous是否不可用
        if($currentPage){
            $disabled = "";
            $href = "javascript:getData(".($currentPage-1).");";
        }else{
            $disabled = "disabled";
            $href = "javascript:void(0);";
        }

        $html .= "<li class='paginate_button previous {$disabled}' id='example1_previous'><a href='{$href}' aria-controls='example1' data-dt-idx='".($currentPage-1)."' tabindex='0'>上一页</a></li>";
        for(; $i < $j; $i ++) {
            $active = $currentPage == $i ? "active" : '';
            $html .= "<li class='paginate_button $active'><a href='javascript:getData($i)' aria-controls='example1' data-dt-idx='$i' tabindex='0'>".($i+1)."</a></li>";
        }
        // next是否不可用
        if($currentPage >= $totalPage-1){
            $disabled = "disabled";
            $href = "javascript:void(0);";
        }else{
            $disabled = "";
            $href = "javascript:getData(".($currentPage+1).");";
        }
        $html .= "<li class='paginate_button next $disabled' id='example1_next'><a href='{$href}' aria-controls='example1' data-dt-idx='".($currentPage+1)."' tabindex='0'>下一页</a></li></ul> </div> </div> </div>";
        return $html;
    }


    /**
     * 生成数据分页索引
     * @param int $limit_arr 分页个数
     * @return string html字串
     */
    public static function pagerLimit($limit_arr) {
        $html = ' <div class="row"> <div class="col-sm-6"> <div class="dataTables_length" id="example1_length"> <label>显示&nbsp;
        <select id="sel_limit" name="example1_length" aria-controls="example1" class="form-control input-sm" onchange="getData();">';
        foreach($limit_arr as $lim){
            $html .= "<option value='{$lim}'>{$lim}</option>";
        }
        $html .= '</select>&nbsp;行</label>';
        $html .= '</div> </div>';

        return $html;
    }

    /**
     * 查IP所在地(需纯真IP数据包)
     * 来自discuz
     */
    public static function ip2addr($ip) {
        //IP数据文件路径
        $dat_path = SYS_DIR . '/wry.dat';

        //检查IP地址
        if (! preg_match ( "/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip )) {
            return 'IP Address Error';
        }
        //打开IP数据文件
        if (! $fd = @fopen ( $dat_path, 'rb' )) {
            return 'IP date file not exists or access denied';
        }

        //分解IP进行运算，得出整形数
        $ip = explode ( '.', $ip );
        $ipNum = $ip [0] * 16777216 + $ip [1] * 65536 + $ip [2] * 256 + $ip [3];

        //获取IP数据索引开始和结束位置
        $DataBegin = fread ( $fd, 4 );
        $DataEnd = fread ( $fd, 4 );
        $ipbegin = implode ( '', unpack ( 'L', $DataBegin ) );
        if ($ipbegin < 0)
            $ipbegin += pow ( 2, 32 );
        $ipend = implode ( '', unpack ( 'L', $DataEnd ) );
        if ($ipend < 0)
            $ipend += pow ( 2, 32 );
        $ipAllNum = ($ipend - $ipbegin) / 7 + 1;

        $BeginNum = 0;
        $EndNum = $ipAllNum;

        $ip1num = 0;
        $ip2num = 0;
        $ipAddr1='';
        $ipAddr2='';
        //使用二分查找法从索引记录中搜索匹配的IP记录
        while ( $ip1num > $ipNum || $ip2num < $ipNum ) {
            $Middle = intval ( ($EndNum + $BeginNum) / 2 );

            //偏移指针到索引位置读取4个字节
            fseek ( $fd, $ipbegin + 7 * $Middle );
            $ipData1 = fread ( $fd, 4 );
            if (strlen ( $ipData1 ) < 4) {
                fclose ( $fd );
                return 'System Error';
            }
            //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
            $ip1num = implode ( '', unpack ( 'L', $ipData1 ) );
            if ($ip1num < 0)
                $ip1num += pow ( 2, 32 );

            //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
            if ($ip1num > $ipNum) {
                $EndNum = $Middle;
                continue;
            }

            //取完上一个索引后取下一个索引
            $DataSeek = fread ( $fd, 3 );
            if (strlen ( $DataSeek ) < 3) {
                fclose ( $fd );
                return 'System Error';
            }
            $DataSeek = implode ( '', unpack ( 'L', $DataSeek . chr ( 0 ) ) );
            fseek ( $fd, $DataSeek );
            $ipData2 = fread ( $fd, 4 );
            if (strlen ( $ipData2 ) < 4) {
                fclose ( $fd );
                return 'System Error';
            }
            $ip2num = implode ( '', unpack ( 'L', $ipData2 ) );
            if ($ip2num < 0)
                $ip2num += pow ( 2, 32 );

            //没找到提示未知
            if ($ip2num < $ipNum) {
                if ($Middle == $BeginNum) {
                    fclose ( $fd );
                    return 'Unknown';
                }
                $BeginNum = $Middle;
            }
        }

        $ipFlag = fread ( $fd, 1 );
        if ($ipFlag == chr ( 1 )) {
            $ipSeek = fread ( $fd, 3 );
            if (strlen ( $ipSeek ) < 3) {
                fclose ( $fd );
                return 'System Error';
            }
            $ipSeek = implode ( '', unpack ( 'L', $ipSeek . chr ( 0 ) ) );
            fseek ( $fd, $ipSeek );
            $ipFlag = fread ( $fd, 1 );
        }

        if ($ipFlag == chr ( 2 )) {
            $AddrSeek = fread ( $fd, 3 );
            if (strlen ( $AddrSeek ) < 3) {
                fclose ( $fd );
                return 'System Error';
            }
            $ipFlag = fread ( $fd, 1 );
            if ($ipFlag == chr ( 2 )) {
                $AddrSeek2 = fread ( $fd, 3 );
                if (strlen ( $AddrSeek2 ) < 3) {
                    fclose ( $fd );
                    return 'System Error';
                }
                $AddrSeek2 = implode ( '', unpack ( 'L', $AddrSeek2 . chr ( 0 ) ) );
                fseek ( $fd, $AddrSeek2 );
            } else {
                fseek ( $fd, - 1, SEEK_CUR );
            }

            while ( ($char = fread ( $fd, 1 )) != chr ( 0 ) )
                $ipAddr2 .= $char;

            $AddrSeek = implode ( '', unpack ( 'L', $AddrSeek . chr ( 0 ) ) );
            fseek ( $fd, $AddrSeek );

            while ( ($char = fread ( $fd, 1 )) != chr ( 0 ) )
                $ipAddr1 .= $char;
        } else {
            fseek ( $fd, - 1, SEEK_CUR );
            while ( ($char = fread ( $fd, 1 )) != chr ( 0 ) )
                $ipAddr1 .= $char;

            $ipFlag = fread ( $fd, 1 );
            if ($ipFlag == chr ( 2 )) {
                $AddrSeek2 = fread ( $fd, 3 );
                if (strlen ( $AddrSeek2 ) < 3) {
                    fclose ( $fd );
                    return 'System Error';
                }
                $AddrSeek2 = implode ( '', unpack ( 'L', $AddrSeek2 . chr ( 0 ) ) );
                fseek ( $fd, $AddrSeek2 );
            } else {
                fseek ( $fd, - 1, SEEK_CUR );
            }
            while ( ($char = fread ( $fd, 1 )) != chr ( 0 ) ) {
                $ipAddr2 .= $char;
            }
        }
        fclose ( $fd );

        //最后做相应的替换操作后返回结果
        if (preg_match ( '/http/i', $ipAddr2 )) {
            $ipAddr2 = '';
        }
        $ipaddr = "$ipAddr1 $ipAddr2";
        $ipaddr = preg_replace ( '/CZ88.Net/is', '', $ipaddr );
        $ipaddr = preg_replace ( '/^s*/is', '', $ipaddr );
        $ipaddr = preg_replace ( '/s*$/is', '', $ipaddr );
        if (preg_match ( '/http/i', $ipaddr ) || $ipaddr == '') {
            $ipaddr = 'Unknown';
        }

        //转成utf8
        return mb_convert_encoding ( $ipaddr, "utf-8", "gbk" );
    }
}
