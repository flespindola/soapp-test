<?php

namespace App\Helpers;

use App\Jobs\Vnstat;

class SystemHelper
{
    static function getFormatedUptime(): string
    {
        $fh = fopen('/proc/uptime', 'r');
        $secsstr = fgets($fh);
        fclose($fh);
        list($secs) = explode(' ', $secsstr);
        $seconds = intval($secs) % 60;
        $minutes = intval($secs / 60) % 60;
        $hours = intval($secs / 60 / 60) % 24;
        $days = floor($secs / 60 / 60 / 24);
        $days = (empty($days) ? '' : "<b>$days</b> " . __('app.day_s') . " ");
        $hours = (empty($hours) ? '' : "<b>$hours</b> " . __('app.hour_s') . " ");
        $minutes = (empty($minutes) ? '' : "<b>$minutes</b> " . __('app.minute_s') . " ");
        $seconds = (empty($seconds) ? '' : "<b>$seconds</b> " . __('app.second_s'));
        return "{$days}{$hours}{$minutes}{$seconds}";
    }

    static function getCpuUsage() : float
    {
        $cpuCores = count(glob("/dev/cpu/*", GLOB_ONLYDIR));
        $cpu_load = sys_getloadavg();
        $cpuusage = round($cpu_load[0] * 100 / $cpuCores, 2);
        return (min($cpuusage, 100));
    }

    static function byte_convert2($bytes, $precision = 2, $nounit = 0, $maxunit = 'MB')
    {
        $units = array('', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $unit = 0;

        do {
            $bytes /= 1024;
            $unit++;
        } while ($units[$unit] != $maxunit);

        if ($nounit == 1) {
            return sprintf("%1.{$precision}f", $bytes);
        } else {
            return sprintf("%1.{$precision}f %s", $bytes, $units[$unit]);
        }
    }

    static function getMemoryUsage(string $type): array
    {
        $mem = shell_exec("free");
        $memArr = explode("\n", $mem);
        $memArray = [];
        foreach ($memArr as $k => $v) {
            if (preg_match("/($type):\s*(\d*)\s*(\d*)\s*(\d*)/", $v, $matches)) {
                $memArray[$matches[1]] = array("total" => $matches[2], "used" => $matches[3], "free" => $matches[4]);
            }
        }
        $mem_info = $memArray[$type];

        $total = self::byte_convert2($mem_info['total'] * 1024, 0, 0);
        $used = self::byte_convert2($mem_info['used'] * 1024, 0, 0);
        $used_perc = $mem_info['used'] / $mem_info['total'];
        $used_perc = (number_format($used_perc, 2)) * 100;

        return [
            'total' => $total,
            'used' => $used,
            'used_perc' => $used_perc,
        ];

    }

    static function getVNStatInfo(string $iface, int $seconds): array
    {
        $vnstat = shell_exec("vnstat -tr {$seconds} -i {$iface} -ru 1");
        $traffic = explode("\n", $vnstat);

        $rx_string = trim($traffic[3]);
        $rx_array = explode(" ", $rx_string);
        foreach ($rx_array as $k => $str) {
            if (trim($str) == "") {
                unset($rx_array[$k]);
            }
        }

        $tx_string = trim($traffic[4]);
        $tx_array = explode(" ", $tx_string);
        foreach ($tx_array as $k => $str) {
            if (empty($str)) {
                unset($tx_array[$k]);
            }
        }

        return [
            'rx' => array_values($rx_array),
            'tx' => array_values($tx_array),
        ];

    }

    static function getVNStatInfoFormated(): array
    {
        $ip_route_command = shell_exec("ip route | grep default | awk '{print $5}'");
        $network_interface = explode("\n", $ip_route_command);
        $network_interface = trim($network_interface[0]);

        $vnstat_info = self::getVNStatInfo($network_interface, 2);

        $info = [];
        $lim = 1024 * 1024; //1Gbit/s

        $directions = ['rx', 'tx'];
        foreach ($directions as $direction) {

            $trafic = $vnstat_info[$direction];

            if (count($trafic) > 3) {
                $x = $val = $trafic[1];
                $unit = $trafic[2];
            } else {
                $x = $val = 0;
                $unit = $trafic[1];
            }

            //Valor em kbit/s
            if ($unit == 'Gbit/s') {
                $x = $x * 1024 * 1024;
            } elseif ($unit == 'Mbit/s') {
                $x = $x * 1024;
            } elseif ($unit == 'bit/s') {
                $x = $x / 1024;
            }

            $x_w = ($x > $lim ? 100.0 : ceil(($x / $lim) * 100));
            if ($x_w == 0) {
                $x_w = 0.2;
            }

            $info[$direction] = [
                'transfer_speed' => $val,
                'transfer_speed_unit' => $unit,
                'transfer_speed_perc' => $x_w,
            ];

        }

        return $info;

    }

    static function getDiskUsage()
    {
        $DEVICES = array("INT" => array(), "EXT" => array());
        $lines = explode("\n", trim(shell_exec("/bin/df /")));
        $DEVICES['INT'][0] = preg_split("/ +/", $lines[count($lines) - 1]);

        //todo - Unidades externas

        $i = 0;
        $disks = [];

        foreach (array_keys($DEVICES) as $devtype) {
            foreach ($DEVICES[$devtype] as $k => $v) {

                $total = self::byte_convert2($v[1] * 1024, 0, 0, 'GB');
                $used = self::byte_convert2($v[2] * 1024, 0, 0, 'GB');
                $w = $v[2] / $v[1];
                $percentage = (number_format($w * 100, 2));
                $devname = ($devtype == 'INT') ? "Interno #" . (++$i) : $v['label'];

                $disks[] = [
                    'device_name' => $devname,
                    'device_total' => $total,
                    'device_used' => $used,
                    'device_used_perc' => $percentage,
                ];

            }
        }

        return $disks;

    }

    static function getSystemInfo(string $info_type): array
    {
        $info = [];

        $types = [
            'cpu_usage',
            'memory_ram_usage',
            'memory_swap_usage',
            'traffic_download',
            'traffic_upload',
            'disk_usage'
        ];
        if(!in_array($info_type, $types)){
            return [];
        }

        switch ($info_type){
            case 'cpu_usage':
                $cpu_usage = self::getCpuUsage();
                $info = [
                    'percentage' => $cpu_usage,
                    'footer_description' => ''
                ];
                break;
            case 'memory_ram_usage':
                $memory_ram_usage =  self::getMemoryUsage('Mem');
                $info = [
                    'percentage' => $memory_ram_usage['used_perc'],
                    'footer_description' => $memory_ram_usage['used'] . " / " . $memory_ram_usage['total']
                ];
                break;
            case 'memory_swap_usage':
                $memory_ram_usage =  self::getMemoryUsage('Swap');
                $info = [
                    'percentage' => $memory_ram_usage['used_perc'],
                    'footer_description' => $memory_ram_usage['used'] . " / " . $memory_ram_usage['total']
                ];
                break;
            case 'traffic_download';
                $vnstat_info = self::getVNStatInfoFormated();
                $info = [
                    'percentage' => $vnstat_info['rx']['transfer_speed_perc'],
                    'footer_description' => $vnstat_info['rx']['transfer_speed'] . " " . $vnstat_info['rx']['transfer_speed_unit']
                ];
                break;
            case 'traffic_upload';
                $vnstat_info = self::getVNStatInfoFormated();
                $info = [
                    'percentage' => $vnstat_info['tx']['transfer_speed_perc'],
                    'footer_description' => $vnstat_info['tx']['transfer_speed'] . " " . $vnstat_info['tx']['transfer_speed_unit']
                ];
                break;
            case 'disk_usage';
                $disks_usage = self::getDiskUsage();
                foreach ($disks_usage as $disk_usage) {
                    $info['items'][] = [
                        'title' => $disk_usage['device_name'],
                        'percentage' => $disk_usage['device_used_perc'],
                        'footer_description' => $disk_usage['device_used'] . " / " . $disk_usage['device_total']
                    ];
                }
                break;
            default:
                $info = [];
                break;
        }

        return $info;

    }

}
