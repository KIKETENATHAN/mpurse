<?php

namespace WhichBrowser\Analyser\Header\Useragent;

use WhichBrowser\Constants;
use WhichBrowser\Data;
use WhichBrowser\Model\Family;
use WhichBrowser\Model\Version;

trait Os
{
    private function &detectOperatingSystem($ua)
    {
        $this->detectUnix($ua);
        $this->detectLinux($ua);
        $this->detectBsd($ua);
        $this->detectDarwin($ua);
        $this->detectWindows($ua);
        $this->detectAndroid($ua);
        $this->detectChromeos($ua);
        $this->detectBlackberry($ua);
        $this->detectWebos($ua);
        $this->detectKaiOS($ua);
        $this->detectSymbian($ua);
        $this->detectNokiaOs($ua);
        $this->detectTizen($ua);
        $this->detectSailfish($ua);
        $this->detectBada($ua);
        $this->detectBrew($ua);
        $this->detectQtopia($ua);
        $this->detectOpenTV($ua);
        $this->detectRemainingOperatingSystems($ua);

        return $this;
    }


    private function &refineOperatingSystem($ua)
    {
        $this->determineAndroidVersionBasedOnBuild($ua);

        return $this;
    }







    /* Darwin */

    private function detectDarwin($ua)
    {
        /* iOS */

        if (preg_match('/\(iOS;/u', $ua)) {
            $this->data->os->name = 'iOS';
            $this->data->device->type = Constants\DeviceType::MOBILE;
        }

        if (preg_match('/(iPhone|iPad|iPod)/u', $ua) && !preg_match('/like iPhone/u', $ua)) {
            $this->data->os->name = 'iOS';

            if (preg_match('/CPU like Mac OS X/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => '1.0' ]);
            }

            if (preg_match('/OS (.*) like Mac OS X/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => str_replace('_', '.', $match[1]) ]);
            }

            if (preg_match('/iPhone OS ([0-9._]*);/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => str_replace('_', '.', $match[1]) ]);
            }

            if (preg_match('/iOS ([0-9.]*);/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);
            }

            if (preg_match('/iPhone Simulator;/u', $ua)) {
                $this->data->device->type = Constants\DeviceType::EMULATOR;
            } else {
                if (preg_match('/(iPad|iPhone( 3GS| 3G| 4S| 4| 5)?|iPod( touch)?)/u', $ua, $match)) {
                    $device = Data\DeviceModels::identify('ios', $match[0]);

                    if ($device) {
                        $this->data->device = $device;
                    }
                }

                if (preg_match('/(iPad|iPhone|iPod)1?[0-9],[0-9][0-9]?/u', $ua, $match)) {
                    $device = Data\DeviceModels::identify('ios', $match[0]);

                    if ($device) {
                        $this->data->device = $device;
                    }
                }
            }
        } elseif (preg_match('/Mac OS X/u', $ua) || preg_match('/;os=Mac/u', $ua)) {
            /* OS X */
            
            $this->data->os->name = 'OS X';

            if (preg_match('/Mac OS X (1[0-9][0-9\._]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => str_replace('_', '.', $match[1]), 'details' => 2 ]);
            }

            if (preg_match('/;os=Mac (1[0-9][0-9[\.,]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => str_replace(',', '.', $match[1]), 'details' => 2 ]);
            }

            if ($this->data->os->version && $this->data->os->version->is('10.16')) {
                $this->data->os->version = new Version([ 'value' => '11.0', 'details' => 2 ]);
            }

            $this->data->device->type = Constants\DeviceType::DESKTOP;
        }

        /* Darwin */

        if (preg_match('/Darwin(?:\/([0-9]+).[0-9]+)?/u', $ua, $match)) {
            if (preg_match('/\(X11;/u', $ua)) {
                /* Darwin */
                $this->data->os->name = 'Darwin';
                $this->data->device->type = Constants\DeviceType::DESKTOP;
            } elseif (preg_match('/\((?:x86_64|i386|Power%20Macintosh)\)/u', $ua)) {
                /* OS X */
                $this->data->os->name = 'OS X';
                $this->data->device->type = Constants\DeviceType::DESKTOP;

                if (isset($match[1])) {
                    $version = Data\Darwin::getVersion('osx', $match[1]);
                    if ($version) {
                        $this->data->os->version = new Version($version);
                    }

                    if (preg_match('/CFNetwork\/([0-9\.]+)/u', $ua, $match)) {
                        $version = Data\CFNetwork::getVersion('osx', $match[1]);
                        if ($version) {
                            $this->data->os->version = new Version($version);
                        }
                    }
                }
            } else {
                /* iOS */
                $this->data->os->name = 'iOS';
                $this->data->device->type = Constants\DeviceType::MOBILE;

                if (isset($match[1])) {
                    $version = Data\Darwin::getVersion('ios', $match[1]);
                    if ($version) {
                        $this->data->os->version = new Version($version);
                    }

                    if (preg_match('/CFNetwork\/([0-9\.]+)/u', $ua, $match)) {
                        $version = Data\CFNetwork::getVersion('ios', $match[1]);
                        if ($version) {
                            $this->data->os->version = new Version($version);
                        }
                    }
                }
            }
        }

        /* Mac OS */

        if (preg_match('/(; |\()Macintosh;/u', $ua) && !preg_match('/OS X/u', $ua)) {
            $this->data->os->name = 'Mac OS';
            $this->data->device->type = Constants\DeviceType::DESKTOP;
        }
    }


    /* Android */

    private function detectAndroid($ua)
    {
        /* Android */

        if (preg_match('/Andr[0o]id/ui', $ua)) {
            $falsepositive = false;

            /* Prevent the Mobile IE 11 Franken-UA from matching Android */
            if (preg_match('/IEMobile\/1/u', $ua)) {
                $falsepositive = true;
            }
            if (preg_match('/Windows Phone 10/u', $ua)) {
                $falsepositive = true;
            }

            /* Prevent Windows 10 IoT Core from matching Android */
            if (preg_match('/Windows IoT/u', $ua)) {
                $falsepositive = true;
            }

            /* Prevent from OSes that claim to be 'like' Android from matching */
            if (preg_match('/like Android/u', $ua)) {
                $falsepositive = true;
            }
            if (preg_match('/COS like Android/u', $ua)) {
                $falsepositive = false;
            }

            if (!$falsepositive) {
                $this->data->os->name = 'Android';
                $this->data->os->version = new Version();

                if (preg_match('/Andr[0o]id(?: )?(?:AllPhone_|CyanogenMod_|OUYA )?(?:\/)?v?([0-9.]+)/ui', str_replace('-update', ',', $ua), $match)) {
                    $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 3 ]);
                }

                if (preg_match('/Android [0-9][0-9].[0-9][0-9].[0-9][0-9]\(([^)]+)\);/u', str_replace('-update', ',', $ua), $match)) {
                    $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 3 ]);
                }

                if (preg_match('/Android Eclair/u', $ua)) {
                    $this->data->os->version = new Version([ 'value' => '2.0', 'details' => 3 ]);
                }

                if (preg_match('/Android KeyLimePie/u', $ua)) {
                    $this->data->os->version = new Version([ 'value' => '4.4', 'details' => 3 ]);
                }

                if (preg_match('/Android (?:L|4.4.99);/u', $ua)) {
                    $this->data->os->version = new Version([ 'value' => '5', 'details' => 3, 'alias' => 'L' ]);
                }

                if (preg_match('/Android (?:M|5.[01].99);/u', $ua)) {
                    $this->data->os->version = new Version([ 'value' => '6', 'details' => 3, 'alias' => 'M' ]);
                }

                if (preg_match('/Android (?:N|6.0.99);/u', $ua)) {
                    $this->data->os->version = new Version([ 'value' => '7', 'details' => 3, 'alias' => 'N' ]);
                }

                $this->data->device->type = Constants\DeviceType::MOBILE;

                if ($this->data->os->version->toFloat() >= 3) {
                    $this->data->device->type = Constants\DeviceType::TABLET;
                }

                if ($this->data->os->version->toFloat() >= 4 && preg_match('/Mobile/u', $ua)) {
                    $this->data->device->type = Constants\DeviceType::MOBILE;
                }

                $candidates = [];

                if (preg_match('/Build/ui', $ua) && (!preg_match('/AppleWebKit.*Build/ui', $ua) || preg_match('/Build.*AppleWebKit/ui', $ua))) {
                    /* Normal Android useragent strings */

                    if (preg_match('/; [a-z][a-zA-Z][-_][a-zA-Z][a-zA-Z] ([^;]*[^;\s])\s+(?:BUILD|Build|build)/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }

                    if (preg_match('/Android [A-Za-z]+; (?:[a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?) Build\/([^\/]*)\//u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }

                    if (preg_match('/;\+? ?(?:\*\*)?([^;]*[^;\s]);?\s+(?:BUILD|Build|build)/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }
                } elseif (preg_match('/\(Linux; Android [0-9\.]+; ([^\/]+)(; wv)?\) AppleWebKit/u', $ua, $match)) {
                    /* New style minimal Android useragent string */

                    $candidates[] = $match[1];
                } elseif (preg_match('/Release\//ui', $ua)) {
                    /* WAP style useragent strings */

                    if (preg_match('/^(?U)([^\/]+)(?U)(?:(?:_CMCC_TD|_CMCC|_TD|_TDLTE|_LTE)?\/[^\/]*)? Linux\/[0-9.+]+ Android\/[0-9.]+/u', $this->removeKnownPrefixes($ua), $match)) {
                        $candidates[] = $match[1];
                    } elseif (preg_match('/^(?U)([^\/]+)(?U)(?:(?:_CMCC_TD|_CMCC|_TD|_TDLTE|_LTE)?\/[^\/]*)? Android(_OS)?\/[0-9.]+/u', $this->removeKnownPrefixes($ua), $match)) {
                        $candidates[] = $match[1];
                    } elseif (preg_match('/^(?U)([^\/]+)(?U)(?:(?:_CMCC_TD|_CMCC|_TD|_TDLTE|_LTE)?\/[^\/]*)? Release\/[0-9.]+/u', $this->removeKnownPrefixes($ua), $match)) {
                        $candidates[] = $match[1];
                    }
                } elseif (preg_match('/Mozilla\//ui', $ua)) {
                    /* Old Android useragent strings */

                    if (preg_match('/Linux; (?:arm; |arm_64; )?(?:U; )?Android [^;]+; (?:[a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?; )?(?:[^;]+; ?)?([^\/;]+)\) /u', $ua, $match)) {
                        $candidates[] = $match[1];
                    } elseif (preg_match('/\(([^;]+);U;Android\/[^;]+;[0-9]+\*[0-9]+;CTC\/2.0\)/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }
                } else {
                    /* Other applications */

                    if (preg_match('/[34]G Explorer\/[0-9.]+ \(Linux;Android [0-9.]+,([^\)]+)\)/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }

                    if (preg_match('/GetJarSDK\/.*android\/[0-9.]+ \([^;]+; [^;]+; ([^\)]+)\)$/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }
                }

                $candidates = array_unique($candidates);

                for ($c = 0; $c < count($candidates); $c++) {
                    if (preg_match('/^[a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?$/u', $candidates[$c])) {
                        unset($candidates[$c]);
                        continue;
                    }

                    if (preg_match('/^Android [0-9\.]+$/u', $candidates[$c])) {
                        unset($candidates[$c]);
                        continue;
                    }

                    /* Ignore "K" or "Unspecified Device" as a device, as it is a dummy value used by Chrome UA reduction */

                    if ($candidates[$c] == 'K' || $candidates[$c] == 'Unspecified Device') {
                        unset($candidates[$c]);
                        continue;
                    }

                    $candidates[$c] = preg_replace('/^[a-zA-Z][a-zA-Z][-_][a-zA-Z][a-zA-Z]\s+/u', '', $candidates[$c]);
                    $candidates[$c] = preg_replace('/(.*) - [0-9\.]+ - (?:with Google Apps - )?API [0-9]+ - [0-9]+x[0-9]+/', '\\1', $candidates[$c]);
                    $candidates[$c] = preg_replace('/^sprd-/u', '', $candidates[$c]);
                    $candidates[$c] = preg_replace('/^HarmonyOS; /u', '', $candidates[$c]);
                    $candidates[$c] = preg_replace('/; GMSCore.*/u', '', $candidates[$c]);
                    $candidates[$c] = preg_replace('/; HMSCore.*/u', '', $candidates[$c]);
                }

                $candidates = array_unique($candidates);

                if (count($candidates)) {
                    $this->data->device->model = $candidates[0];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    for ($c = 0; $c < count($candidates); $c++) {
                        $device = Data\DeviceModels::identify('android', $candidates[$c]);
                        if ($device->identified) {
                            $device->identified |= $this->data->device->identified;
                            $this->data->device = $device;
                            break;
                        }
                    }
                }

                if (preg_match('/HP eStation/u', $ua)) {
                    $this->data->device->manufacturer = 'HP';
                    $this->data->device->model = 'eStation';
                    $this->data->device->type = Constants\DeviceType::PRINTER;
                    $this->data->device->identified |= Constants\Id::MATCH_UA;
                    $this->data->device->generic = false;
                }
            }
        }

        if (preg_match('/\(Linux; (?:U; )?(?:([0-9.]+); )?(?:[a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?; )?([^;]+) Build/u', $ua, $match)) {
            $falsepositive = false;

            if ($match[2] == 'OpenTV') {
                $falsepositive = true;
            }

            if (!$falsepositive) {
                $this->data->device->type = Constants\DeviceType::MOBILE;
                $this->data->device->model = $match[2];

                $this->data->os->name = 'Android';

                if (!empty($match[1])) {
                    $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 3 ]);
                }

                $device = Data\DeviceModels::identify('android', $match[2]);
                if ($device->identified) {
                    $device->identified |= Constants\Id::PATTERN;
                    $device->identified |= $this->data->device->identified;

                    $this->data->device = $device;
                }
            }
        }

        if (preg_match('/Linux x86_64; ([^;\)]+)(?:; [a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?)?\) AppleWebKit\/534.24 \(KHTML, like Gecko\) Chrome\/11.0.696.34 Safari\/534.24/u', $ua, $match)) {
            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= Constants\Id::PATTERN;
                $device->identified |= $this->data->device->identified;

                $this->data->os->name = 'Android';
                $this->data->device = $device;
            }
        }

        if (preg_match('/\(Linux; U; Linux Ventana; [^;]+; ([^;]+) Build/u', $ua, $match)) {
            $this->data->device->type = Constants\DeviceType::MOBILE;
            $this->data->device->model = $match[1];

            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= Constants\Id::PATTERN;
                $device->identified |= $this->data->device->identified;

                $this->data->os->name = 'Android';
                $this->data->device = $device;
            }
        }


        /* Harmony OS */

        if (preg_match('/HarmonyOS/u', $ua)) {
            $this->data->os->name = 'Harmony OS';
            $this->data->os->version = new Version();


            if (preg_match('/; Android ([0-9\.]+);/u', $ua, $match)) {
                $this->data->os->family = new Family([
                    'name' => 'Android',
                    'version' => new Version([ 'value' => $match[1], 'details' => 3 ])
                ]);
            }
        }


        /* Aliyun OS */

        if (preg_match('/Aliyun/u', $ua) || preg_match('/YunOs/ui', $ua)) {
            $this->data->os->name = 'Aliyun OS';
            $this->data->os->family = new Family([ 'name' => 'Android' ]);
            $this->data->os->version = new Version();

            if (preg_match('/YunOs[ \/]([0-9.]+)/iu', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 3 ]);
            }

            if (preg_match('/AliyunOS ([0-9.]+)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 3 ]);
            }

            $this->data->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/; ([^;]*[^;\s])\s+Build/u', $ua, $match)) {
                $this->data->device->model = $match[1];
            }

            if (isset($this->data->device->model)) {
                $this->data->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $this->data->device->model);
                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }
        }

        if (preg_match('/Android/u', $ua)) {
            if (preg_match('/Android v(1.[0-9][0-9])_[0-9][0-9].[0-9][0-9]-/u', $ua, $match)) {
                $this->data->os->name = 'Aliyun OS';
                $this->data->os->family = new Family([ 'name' => 'Android' ]);
                $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 3 ]);
            }

            if (preg_match('/Android[ \/](1.[0-9].[0-9].[0-9]+)-R?T/u', $ua, $match)) {
                $this->data->os->name = 'Aliyun OS';
                $this->data->os->family = new Family([ 'name' => 'Android' ]);
                $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 3 ]);
            }

            if (preg_match('/Android ([12].[0-9].[0-9]+)-R-20[0-9]+/u', $ua, $match)) {
                $this->data->os->name = 'Aliyun OS';
                $this->data->os->family = new Family([ 'name' => 'Android' ]);
                $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 3 ]);
            }

            if (preg_match('/Android 20[0-9]+\./u', $ua, $match)) {
                $this->data->os->name = 'Aliyun OS';
                $this->data->os->family = new Family([ 'name' => 'Android' ]);
                $this->data->os->version = null;
            }
        }

        /* Baidu Yi */

        if (preg_match('/Baidu Yi/u', $ua)) {
            $this->data->os->name = 'Baidu Yi';
            $this->data->os->family = new Family([ 'name' => 'Android' ]);
            $this->data->os->version = null;
        }

        /* Google TV */

        if (preg_match('/GoogleTV/u', $ua)) {
            $this->data->os->name = 'Google TV';
            $this->data->os->family = new Family([ 'name' => 'Android' ]);

            $this->data->device->type = Constants\DeviceType::TELEVISION;

            if (preg_match('/GoogleTV [0-9\.]+; ?([^;]*[^;\s])\s+Build/u', $ua, $match)) {
                $this->data->device->model = $match[1];
            }

            if (isset($this->data->device->model) && $this->data->device->model) {
                $this->data->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $this->data->device->model);
                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }
        }

        /* LeOS */

        if (preg_match('/LeOS/u', $ua)) {
            $this->data->os->name = 'LeOS';
            $this->data->os->family = new Family([ 'name' => 'Android' ]);

            if (preg_match('/LeOS([0-9\.]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);
            }

            $this->data->device->type = Constants\DeviceType::TABLET;

            if (preg_match('/LeOS[0-9\.]+; [^;]+; (.*) Build/u', $ua, $match)) {
                $this->data->device->model = $match[1];
            }

            if (isset($this->data->device->model) && $this->data->device->model) {
                $this->data->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $this->data->device->model);
                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }
        }

        /* WoPhone */

        if (preg_match('/WoPhone/u', $ua)) {
            $this->data->os->name = 'WoPhone';
            $this->data->os->family = new Family([ 'name' => 'Android' ]);

            if (preg_match('/WoPhone\/([0-9\.]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);
            }

            $this->data->device->type = Constants\DeviceType::MOBILE;
        }

        /* COS */

        if (preg_match('/(COS|(China|Chinese) Operating System)/ui', $ua)) {
            if (preg_match('/COS[\/ ]?([0-9]\.[0-9.]+)/ui', $ua, $match)) {
                $this->data->os->name = 'COS';
                $this->data->os->family = new Family([ 'name' => 'Android' ]);
                $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 2 ]);
            } elseif (preg_match('/(?:\(|; )(?:China|Chinese) Operating System ([0-9]\.[0-9.]*);/ui', $ua, $match)) {
                $this->data->os->name = 'COS';
                $this->data->os->family = new Family([ 'name' => 'Android' ]);
                $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 2 ]);
            } elseif (preg_match('/COS like Android/ui', $ua, $match)) {
                $this->data->os->name = 'COS';
                $this->data->os->family = new Family([ 'name' => 'Android' ]);
                $this->data->os->version = null;
                $this->data->device->type = Constants\DeviceType::MOBILE;
            } elseif (preg_match('/(COS like Android|COSBrowser\/|\(COS;|\(COS 998;)/ui', $ua, $match)) {
                $this->data->os->name = 'COS';
                $this->data->os->family = new Family([ 'name' => 'Android' ]);
            }
        }

        /* RemixOS */

        if (preg_match('/RemixOS/u', $ua)) {
            $this->data->os->name = 'Remix OS';
            $this->data->os->version = null;
            $this->data->os->family = new Family([ 'name' => 'Android' ]);

            if (preg_match('/RemixOS ([0-9]\.[0-9])/u', $ua, $match)) {
                switch ($match[1]) {
                    case '5.1':
                        $this->data->os->version = new Version([ 'value' => '1.0' ]);
                        break;
                    case '6.0':
                        $this->data->os->version = new Version([ 'value' => '2.0' ]);
                        break;
                }
            }

            $this->data->device->type = Constants\DeviceType::DESKTOP;
        }
    }

    private function determineAndroidVersionBasedOnBuild($ua)
    {
        if ($this->data->isOs('Android')) {
            if (preg_match('/Build\/([^\);]+)/u', $ua, $match)) {
                $version = Data\BuildIds::identify($match[1]);
                if ($version) {
                    if (!isset($this->data->os->version) || $this->data->os->version == null || $this->data->os->version->value == null || $version->toFloat() < $this->data->os->version->toFloat()) {
                        $this->data->os->version = $version;
                    }

                    /* Special case for Android L */
                    if ($version->toFloat() == 5) {
                        $this->data->os->version = $version;
                    }
                }

                $this->data->os->build = $match[1];
            }
        }
    }


    /* Windows */

    private function detectWindows($ua)
    {
        if (preg_match('/(Windows|WinNT|WinCE|WinMobile|Win ?[9MX]|Win(16|32))/u', $ua)) {
            $this->data->os->name = 'Windows';
            $this->data->device->type = Constants\DeviceType::DESKTOP;


            /* Windows NT */

            if (preg_match('/Windows 2000/u', $ua)) {
                $this->data->os->version = new Version([ 'value' => '5.0', 'alias' => '2000' ]);
            }

            if (preg_match('/(Windows XP|WinXP)/u', $ua)) {
                $this->data->os->version = new Version([ 'value' => '5.1', 'alias' => 'XP' ]);
            }

            if (preg_match('/Windows Vista/u', $ua)) {
                $this->data->os->version = new Version([ 'value' => '6.0', 'alias' => 'Vista' ]);
            }

            if (preg_match('/(?:Windows NT |WinNT)([0-9][0-9]?\.[0-9])/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);

                switch ($match[1]) {
                    case '10.1':
                    case '10.0':
                    case '6.4':
                        $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => '10' ]);
                        break;

                    case '6.3':
                        if (preg_match('/; ARM;/u', $ua)) {
                            $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => 'RT 8.1' ]);
                        } else {
                            $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => '8.1' ]);
                        }
                        break;

                    case '6.2':
                        if (preg_match('/; ARM;/u', $ua)) {
                            $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => 'RT' ]);
                        } else {
                            $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => '8' ]);
                        }
                        break;

                    case '6.1':
                        $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => '7' ]);
                        break;
                    case '6.0':
                        $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => 'Vista' ]);
                        break;
                    case '5.2':
                        $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => 'Server 2003' ]);
                        break;
                    case '5.1':
                        $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => 'XP' ]);
                        break;
                    case '5.0':
                        $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => '2000' ]);
                        break;
                    default:
                        $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => 'NT ' . $match[1] ]);
                        break;
                }

                $this->detectWindowsOemManufacturer($ua);
            }


            /* Windows 10 IoT Core */

            if (preg_match('/Windows IoT (1[0-9]\.[0-9]);/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1], 'alias' => '10 IoT Core' ]);
            }


            /* Windows */

            if (preg_match('/(Windows 95|Win95)/u', $ua)) {
                $this->data->os->version = new Version([ 'value' => '4.0', 'alias' => '95' ]);
            }

            if (preg_match('/(Windows 98|Win98)/u', $ua)) {
                $this->data->os->version = new Version([ 'value' => '4.1', 'alias' => '98' ]);
            }

            if (preg_match('/(Windows M[eE]|WinME)/u', $ua)) {
                $this->data->os->version = new Version([ 'value' => '4.9', 'alias' => 'ME' ]);
            }

            if (preg_match('/(?:Windows|Win 9x) (([1234]\.[0-9])[0-9\.]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);

                switch ($match[2]) {
                    case '4.0':
                        $this->data->os->version = new Version([ 'value' => '4.0', 'alias' => '95' ]);
                        break;
                    case '4.1':
                        $this->data->os->version = new Version([ 'value' => '4.1', 'alias' => '98' ]);
                        break;
                    case '4.9':
                        $this->data->os->version = new Version([ 'value' => '4.9', 'alias' => 'ME' ]);
                        break;
                }
            }


            /* Windows Mobile and Windows Phone */

            if (preg_match('/WPDesktop/u', $ua)) {
                $this->data->os->name = 'Windows Phone';
                $this->data->os->version = new Version([ 'value' => '8.0', 'details' => 2 ]);
                $this->data->device->type = Constants\DeviceType::MOBILE;
                $this->data->browser->mode = 'desktop';
            }

            if (preg_match('/WP7/u', $ua)) {
                $this->data->os->name = 'Windows Phone';
                $this->data->os->version = new Version([ 'value' => '7', 'details' => 1 ]);
                $this->data->device->type = Constants\DeviceType::MOBILE;
                $this->data->browser->mode = 'desktop';
            }

            if (preg_match('/WinMobile/u', $ua)) {
                $this->data->os->name = 'Windows Mobile';
                $this->data->device->type = Constants\DeviceType::MOBILE;

                if (preg_match('/WinMobile\/([0-9.]*)/u', $ua, $match)) {
                    $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 2 ]);
                }
            }

            if (preg_match('/(Windows CE|WindowsCE|WinCE)/u', $ua)) {
                $this->data->device->type = Constants\DeviceType::MOBILE;

                if (preg_match('/ IEMobile/u', $ua)) {
                    $this->data->os->name = 'Windows Mobile';

                    if (preg_match('/ IEMobile\/9/u', $ua)) {
                        $this->data->os->name = 'Windows Phone';
                        $this->data->os->version = new Version([ 'value' => '7.5', 'details' => 2 ]);
                    }

                    if (preg_match('/ IEMobile 8/u', $ua)) {
                        $this->data->os->version = new Version([ 'value' => '6.5', 'details' => 2 ]);
                    }

                    if (preg_match('/ IEMobile 7/u', $ua)) {
                        $this->data->os->version = new Version([ 'value' => '6.1', 'details' => 2 ]);
                    }

                    if (preg_match('/ IEMobile 6/u', $ua)) {
                        $this->data->os->version = new Version([ 'value' => '6.0', 'details' => 2 ]);
                    }
                } else {
                    $this->data->os->name = 'Windows CE';

                    if (preg_match('/WindowsCEOS\/([0-9.]*)/u', $ua, $match)) {
                        $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 2 ]);
                    }

                    if (preg_match('/Windows CE ([0-9.]*)/u', $ua, $match)) {
                        $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 2 ]);
                    }
                }


                $model = null;

                if (empty($model) && preg_match('/IEMobile [0-9.]+\)  ?(?:PPC; |SP; |Smartphone; )?(?:[0-9]+[Xx][0-9]+;? )?(?:VZW; )?([^;\(]+)/u', $ua, $match)) {
                    if (!preg_match('/(Profile\/MIDP|UNTRUSTED)/u', $match[1])) {
                        $model = $match[1];
                    }
                }

                if (empty($model) && preg_match('/IEMobile [0-9.]+\) (?:PPC|SP|Smartphone); (?:[0-9]+[Xx][0-9]+;? )([^;]+) Profile\/MIDP/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; (?:PPC|SP|Smartphone); [0-9]+x[0-9]+; ([^;\)]+)\)$/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; (?:PPC|SP|Smartphone); [0-9]+x[0-9]+; ([^;]+); (?:PPC|OpVer)/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; (?:PPC|SP|Smartphone); ([^;]+) Profile\/MIDP/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; (?:PPC|SP|Smartphone) ([^;\(]+)[;\/] [0-9]+x[0-9]+/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; ([^;\(]+); [0-9]+x[0-9]+\)/u', $ua, $match)) {
                    if (!preg_match('/^(Smartphone|PPC$)/u', $match[1])) {
                        $model = $match[1];
                    }
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; ([^;\(]+);? ?(?:PPC|SP|Smartphone); ?[0-9]+x[0-9]+/u', $ua, $match)) {
                    if (!preg_match('/^(MIDP-2.0)/u', $match[1])) {
                        $model = $match[1];
                    }
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; ([^;\)]+)(?:; (?:PPC|SP|Smartphone); [0-9]+x[0-9]+)?\)( \[[a-zA-Z\-]+\])?$/u', $ua, $match)) {
                    if (!preg_match('/^(IEMobile|MIDP-2.0|Smartphone|PPC$)/u', $match[1])) {
                        $model = $match[1];
                    }
                }

                if (!empty($model)) {
                    $model = preg_replace('/(HTC\/|Toshiba\/)/', '', $model);

                    $this->data->device->model = $model;
                    $this->data->device->identified |= Constants\Id::PATTERN;
                    $this->data->os->name = 'Windows Mobile';

                    $device = Data\DeviceModels::identify('wm', $model);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                } else {
                    if (empty($model) && preg_match('/Windows CE [^;]+; Trident\/[^;]+; IEMobile[\/ ][^;]+[\);] ([A-Z\s]+); ?([^\/\),]+)/ui', $ua, $match)) {
                        $model = $match[2];
                    }

                    if (!empty($model)) {
                        $this->data->device->model = $model;
                        $this->data->device->identified |= Constants\Id::PATTERN;
                        $this->data->os->name = 'Windows Phone';

                        $device = Data\DeviceModels::identify('wp', $model);
                        if ($device->identified) {
                            $device->identified |= $this->data->device->identified;
                            $this->data->device = $device;
                        }
                    }
                }
            }

            if (preg_match('/Microsoft Windows; (PPC|Smartphone)/u', $ua)) {
                $this->data->os->name = 'Windows Mobile';
                $this->data->device->type = Constants\DeviceType::MOBILE;
            }

            if (preg_match('/Windows CE; (PPC|Smartphone)/u', $ua)) {
                $this->data->os->name = 'Windows Mobile';
                $this->data->device->type = Constants\DeviceType::MOBILE;
            }


            /* Detect models in common places */

            if (preg_match('/Windows ?Mobile/u', $ua)) {
                $this->data->os->name = 'Windows Mobile';
                $this->data->device->type = Constants\DeviceType::MOBILE;

                if (preg_match('/Windows ?Mobile[\/ ]([0-9.]*)/u', $ua, $match)) {
                    $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 2 ]);
                }

                if (preg_match('/Windows Mobile; (?:SHARP\/)?([^;]+); (?:PPC|Smartphone);/u', $ua, $match)) {
                    $this->data->device->model = $match[1];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wm', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                if (preg_match('/\(([^;]+); U; Windows Mobile/u', $ua, $match)) {
                    $this->data->device->model = $match[1];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wm', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }
            }

            if (preg_match('/(Windows Phone|Windows NT 1[0-9]\.[0-9]; ARM|WPDesktop|ZuneWP7)/u', $ua)) {
                $this->data->os->name = 'Windows Phone';
                $this->data->device->type = Constants\DeviceType::MOBILE;

                if (preg_match('/Windows Phone(?: OS)?[ \/]([0-9.]*)/u', $ua, $match)) {
                    $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 2 ]);

                    if (intval($match[1]) < 7) {
                        $this->data->os->name = 'Windows Mobile';
                    }
                }

                /* Windows Mobile 6.5 */
                if (preg_match('/Windows NT 5.1; ([^;]+); Windows Phone/u', $ua, $match)) {
                    $this->data->device->model = $match[1];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wm', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                /* Windows Phone 7 (buggy) */
                if (preg_match('/Windows Phone OS [^;]+; Trident\/[^;]+; IEMobile[\/ ][^;]+[\);] ([A-Z\s]+); ?([^\/\),]+)/ui', $ua, $match)) {
                    $this->data->device->manufacturer = $match[1];
                    $this->data->device->model = $match[2];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                /* Windows Phone 7 and 8 */
                if (preg_match('/IEMobile\/[^;]+;(?: ARM; Touch; )?(?:rv:[0-9]+; )?(?: WpsLondonTest; )?\s*([^;\s][^;\)]*);\s*([^;\)\s][^;\)]*)[;|\)]/u', $ua, $match)) {
                    $this->data->device->manufacturer = $match[1];
                    $this->data->device->model = $match[2];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                /* Windows Phone 10 */
                if (preg_match('/Windows Phone 1[0-9]\.[0-9]; Android [0-9\.]+; (?:WebView\/[0-9\.]+; )?([^;\s][^;]*);\s*([^;\)\s][^;\)]*)[;|\)]/u', $ua, $match)) {
                    $this->data->device->manufacturer = $match[1];
                    $this->data->device->model = $match[2];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                /* Windows Phone 10 Continuum */
                if (preg_match('/Windows NT 1[0-9]\.[0-9]; ARM; ([^;\)\s][^;\)]*)\)/u', $ua, $match)) {
                    $this->data->device->model = $match[1];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }

                    $this->data->device->type = Constants\DeviceType::DESKTOP;
                }

                /* Third party browsers */
                if (preg_match('/IEMobile\/[^;]+;(?: ARM; Touch; )?\s*(?:[^\/]+\/[^\/]+);\s*([^;\s][^;]*);\s*([^;\)\s][^;\)]*)[;|\)]/u', $ua, $match)) {
                    $this->data->device->manufacturer = $match[1];
                    $this->data->device->model = $match[2];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                if (preg_match('/\(Windows Phone OS\/[0-9\.]+; ([^:]+):([^;]+); [a-z]+(?:\-[a-z]+)?\)/iu', $ua, $match)) {
                    $this->data->device->manufacturer = $match[1];
                    $this->data->device->model = $match[2];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                /* Desktop mode of WP 8.1 */
                if (preg_match('/WPDesktop;\s*([^;\)]*)(?:;\s*([^;\)]*))?(?:;\s*([^;\)]*))?\) like Gecko/u', $ua, $match)) {
                    if (preg_match("/^[A-Z]+$/", $match[1]) && isset($match[2])) {
                        $this->data->device->manufacturer = $match[1];
                        $this->data->device->model = $match[2];
                    } else {
                        $this->data->device->model = $match[1];
                    }

                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $this->data->device->model);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                /* Desktop mode of WP 7 */
                if (preg_match('/XBLWP7; ZuneWP7; ([^\)]+)\)/u', $ua, $match)) {
                    $this->data->device->model = $match[1];
                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                /* Desktop mode of WP 8.0 and 8.1 Update (buggy version) */
                if (preg_match('/Touch; WPDesktop;\s*([^;\)]*)(?:;\s*([^;\)]*))?(?:;\s*([^;\)]*))?\)/u', $ua, $match)) {
                    if (preg_match("/^[A-Z]+$/", $match[1]) && isset($match[2])) {
                        $this->data->device->manufacturer = $match[1];
                        $this->data->device->model = $match[2];
                    } else {
                        $this->data->device->model = $match[1];
                    }

                    $this->data->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $this->data->device->model);
                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }

                if (isset($this->data->device->manufacturer) && isset($this->data->device->model)) {
                    if ($this->data->device->manufacturer == 'ARM' && $this->data->device->model == 'Touch') {
                        $this->data->device->manufacturer = null;
                        $this->data->device->model = null;
                        $this->data->device->identified = Constants\Id::NONE;
                    }

                    if ($this->data->device->model == 'XDeviceEmulator') {
                        $this->data->device->manufacturer = null;
                        $this->data->device->model = null;
                        $this->data->device->type = Constants\DeviceType::EMULATOR;
                        $this->data->device->identified |= Constants\Id::MATCH_UA;
                    }
                }
            }
        } elseif (preg_match('/WMPRO/u', $ua)) {
            $this->data->os->name = 'Windows Mobile';
            $this->data->device->type = Constants\DeviceType::MOBILE;
        }
    }

    private function detectWindowsOemManufacturer($ua)
    {
        $manufacturers = [
            'MAAR'      => 'Acer',
            'ASJB'      => 'Asus',
            'ASU2'      => 'Asus',
            'MAAU'      => 'Asus',
            'NP06'      => 'Asus',
            'NP07'      => 'Asus',
            'NP08'      => 'Asus',
            'NP09'      => 'Asus',
            'CMNTDF'    => 'Compaq',
            'CPDTDF'    => 'Compaq',
            'CPNTDF'    => 'Compaq',
            'MDDR'      => 'Dell',
            'MDDC'      => 'Dell',
            'MDDS'      => 'Dell',
            'FSJB'      => 'Fujitsu',
            'MAFS'      => 'Fujitsu',
            'MAGW'      => 'Gateway',
            'HPCMHP'    => 'HP',
            'HPDTDF'    => 'HP',
            'HPNTDF'    => 'HP',
            'MANM'      => 'Hyrican',
            'LCJB'      => 'Lenovo',
            'LEN2'      => 'Lenovo',
            'MALC'      => 'Lenovo',
            'MALE'      => 'Lenovo',
            'MALN'      => 'Lenovo',
            'MAMD'      => 'Medion',
            'MAMI'      => 'MSI',
            'MAM3'      => 'MSI',
            'MASM'      => 'Samsung',
            'SMJB'      => 'Samsung',
            'MASA'      => 'Sony',
            'MASE'      => 'Sony',
            'MASP'      => 'Sony',
            'MATB'      => 'Toshiba',
            'MATM'      => 'Toshiba',
            'MATP'      => 'Toshiba',
            'TAJB'      => 'Toshiba',
            'TNJB'      => 'Toshiba',
        ];

        $keys = array_keys($manufacturers);

        if (preg_match('/; (' . implode('|', $keys) . ')(?:JS)?[\);]/u', $ua, $match)) {
            $this->data->device->manufacturer = $manufacturers[$match[1]];
            $this->data->device->hidden = true;
            $this->data->device->identified |= Constants\Id::INFER;
        }
    }


    /* Jolla Sailfish */

    private function detectSailfish($ua)
    {
        if (preg_match('/Sailfish;/u', $ua)) {
            $this->data->os->name = 'Sailfish';
            $this->data->os->version = null;

            if (preg_match('/Jolla;/u', $ua)) {
                $this->data->device->manufacturer = 'Jolla';
            }

            if (preg_match('/Mobile/u', $ua)) {
                $this->data->device->model = 'Phone';
                $this->data->device->type = Constants\DeviceType::MOBILE;
                $this->data->device->identified = Constants\Id::PATTERN;
            }

            if (preg_match('/Tablet/u', $ua)) {
                $this->data->device->model = 'Tablet';
                $this->data->device->type = Constants\DeviceType::TABLET;
                $this->data->device->identified = Constants\Id::PATTERN;
            }
        }
    }


    /* Bada */

    private function detectBada($ua)
    {
        if (preg_match('/[b|B]ada/u', $ua)) {
            $this->data->os->name = 'Bada';

            if (preg_match('/[b|B]ada[\/ ]([0-9.]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1], 'details' => 2 ]);
            }

            $this->data->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/\(([^;]+); ([^\/]+)\//u', $ua, $match)) {
                if ($match[1] != 'Bada') {
                    $this->data->device->manufacturer = $match[1];
                    $this->data->device->model = $match[2];
                    $this->data->device->identified = Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('bada', $match[2]);

                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }
            }
        }
    }


    /* Tizen */

    private function detectTizen($ua)
    {
        if (preg_match('/Tizen/u', $ua)) {
            $this->data->os->name = 'Tizen';

            if (preg_match('/Tizen[\/ ]?([0-9.]*[0-9])/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);
            }

            if (preg_match('/\(([^;]+); ([^\/]+)\//u', $ua, $match)) {
                $falsepositive = false;
                if (strtoupper($match[1]) == 'SMART-TV') {
                    $falsepositive = true;
                }
                if ($match[1] == 'TV') {
                    $falsepositive = true;
                }
                if ($match[1] == 'Linux') {
                    $falsepositive = true;
                }
                if ($match[1] == 'Tizen') {
                    $falsepositive = true;
                }

                if (!$falsepositive) {
                    $this->data->device->manufacturer = $match[1];
                    $this->data->device->model = $match[2];
                    $this->data->device->identified = Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('tizen', $match[2]);

                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }
            }

            if (preg_match('/\s*([^;]+);\s+([^;\)]+)\)/u', $ua, $match)) {
                $falsepositive = false;
                if ($match[1] == 'U') {
                    $falsepositive = true;
                }
                if (substr($match[2], 0, 5) == 'Tizen') {
                    $falsepositive = true;
                }
                if (substr($match[2], 0, 11) == 'AppleWebKit') {
                    $falsepositive = true;
                }
                if (preg_match("/^[a-z]{2,2}(?:\-[a-z]{2,2})?$/", $match[2])) {
                    $falsepositive = true;
                }

                if (!$falsepositive) {
                    $this->data->device->model = $match[2];
                    $this->data->device->identified = Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('tizen', $match[2]);

                    if ($device->identified) {
                        $device->identified |= $this->data->device->identified;
                        $this->data->device = $device;
                    }
                }
            }


            if (!$this->data->device->type && preg_match('/Mobile/iu', $ua, $match)) {
                $this->data->device->type = Constants\DeviceType::MOBILE;
            }


            if (preg_match('/\((SMART[ -])?TV;/iu', $ua, $match)) {
                $this->data->device->type = Constants\DeviceType::TELEVISION;
                $this->data->device->manufacturer = 'Samsung';
                $this->data->device->series = 'Smart TV';
                $this->data->device->identified = Constants\Id::PATTERN;
            }


            if (preg_match('/(?:Samsung|Tizen ?)Browser\/([0-9.]*)/u', $ua, $match)) {
                $this->data->browser->name = "Samsung Browser";
                $this->data->browser->channel = null;
                $this->data->browser->stock = true;
                $this->data->browser->version = new Version([ 'value' => $match[1] ]);
                $this->data->browser->channel = null;
            }
        }

        if (preg_match('/Linux\; U\; Android [0-9.]+\; ko\-kr\; SAMSUNG\; (NX[0-9]+[^\)]]*)/u', $ua, $match)) {
            $this->data->os->name = 'Tizen';
            $this->data->os->version = null;

            $this->data->device->type = Constants\DeviceType::CAMERA;
            $this->data->device->manufacturer = 'Samsung';
            $this->data->device->model = $match[1];
            $this->data->device->identified = Constants\Id::PATTERN;
        }
    }


    /* Symbian */

    private function detectSymbian($ua)
    {
        if (!preg_match('/(EPOC|Series|Symbian|S60|UIQ)/ui', $ua)) {
            return;
        }

        /* EPOC */

        if (preg_match('/EPOC(?:32)?[;\-\)]/u', $ua, $match)) {
            $this->data->os->name = 'EPOC';
            $this->data->os->family = new Family([ 'name' => 'Symbian' ]);
            $this->data->device->type = Constants\DeviceType::PDA;

            if (preg_match('/Crystal\/([0-9.]*)/u', $ua, $match)) {
                $this->data->os->name = 'Series80';
                $this->data->os->version = new Version([ 'value' => '1.0' ]);
                $this->data->os->family->version = new Version([ 'value' => $match[1] ]);
                $this->data->device->type = Constants\DeviceType::MOBILE;

                $this->data->device->manufacturer = 'Nokia';
                $this->data->device->model = '9210';
                $this->data->device->identified |= Constants\Id::PATTERN;
            }

            if (preg_match('/Nokia\/Series-9200/u', $ua)) {
                $this->data->os->name = 'Series80';
                $this->data->os->version = new Version([ 'value' => '1.0' ]);
                $this->data->device->type = Constants\DeviceType::MOBILE;

                $this->data->device->manufacturer = 'Nokia';
                $this->data->device->model = '9210i';
                $this->data->device->identified |= Constants\Id::PATTERN;
            }
        }

        /* Series 80 */

        if (preg_match('/Series80\/([0-9.]*)/u', $ua, $match)) {
            $this->data->os->name = 'Series80';
            $this->data->os->version = new Version([ 'value' => $match[1] ]);
            $this->data->os->family = new Family([ 'name' => 'Symbian' ]);
            $this->data->device->type = Constants\DeviceType::MOBILE;
        }

        /* Series 60 */

        if (preg_match('/Symbian\/3/u', $ua)) {
            $this->data->os->name = 'Series60';
            $this->data->os->version = new Version([ 'value' => '5.2' ]);
            $this->data->os->family = new Family([ 'name' => 'Symbian' ]);
            $this->data->device->type = Constants\DeviceType::MOBILE;
        }

        if (preg_match('/Series[ ]?60/u', $ua) || preg_match('/S60[V\/;]/u', $ua) || preg_match('/S60 Symb/u', $ua)) {
            $this->data->os->name = 'Series60';
            $this->data->os->family = new Family([ 'name' => 'Symbian' ]);
            $this->data->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/Series60\/([0-9.]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);
            }

            if (preg_match('/S60\/([0-9.]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);
            }

            if (preg_match('/S60V([0-9.]*)/u', $ua, $match)) {
                $this->data->os->version = new Version([ 'value' => $match[1] ]);
            }
        }

        /* UIQ */

        if (preg_match('/UIQ\/([0-9.]*)/u', $ua, $match)) {
            $this->data->os->name = 'UIQ';
            $this->data->os->version = new Version([ 'value' => $match[1] ]);
            $this->data->os->family = new Family([ 'name' => 'Symbian' ]);
            $this->data->device->type = Constants\DeviceType::MOBILE;
        }

        /* Symbian */

        if (preg_match('/Symbian/u', $ua)) {
            $this->data->os->family = new Family([ 'name' => 'Symbian' ]);
            $this->data->device->type = Constants\DeviceType::MOBILE;
            
            if (preg_match('/SymbianOS\/([0-9.]*)/u', $ua, $match)) {
                $this->data->os->family->version = new Version([ 'value' => $match[1] ]);
            }
        }


        if ($this->data->os->isFamily('Symbian')) {
            if (preg_match('/Nokia-?([^\/;\)\s]+)[\s|\/|;|\)]/u', $ua, $match)) {
                if ($match[1] != 'Browser') {
                    $this->data->device->manufacturer = 'Nokia';
                    $this->data->device->model = Data\DeviceModels::cleanup($match[1]);
                    $this->data->device->identified |= Constants\Id::PATTERN;
                }
            }

            if (preg_match('/Symbian(?:\/3)?; U; (?:Nokia)?([^;]+); [a-z][a-z](?:\-[a-z][a-z])?/u', $ua, $match)) {
                $this->data->device->manufacturer = 'Nokia';
                $this->data->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->data->device->identified |= Constants\Id::PATTERN;
            }

            if (preg_match('/Vertu([^\/;]+)[\/|;]/u', $ua, $match)) {
                $this->data->device->manufacturer = 'Vertu';
                $this->data->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->data->device->identified |= Constants\Id::PATTERN;
            }

            if (preg_match('/Samsung\/([^;]*);/u', $ua, $match)) {
                $this->data->device->manufacturer = 'Samsung';
                $this->data->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->data->device->identified |= Constants\Id::PATTERN;
            }

            if (isset($this->data->device->model)) {
                $device = Data\DeviceModels::identify('symbian', $this->data->device->model);
                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }
        }
    }

    private function detectNokiaOs($ua)
    {
        if (!preg_match('/(Series|MeeGo|Maemo|Geos)/ui', $ua)) {
            return;
        }

        /* Series 40 */

        if (preg_match('/Series40/u', $ua)) {
            $this->data->os->name = 'Series40';

            if (preg_match('/Nokia([^\/]+)\//u', $ua, $match)) {
                $this->data->device->manufacturer = 'Nokia';
                $this->data->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->data->device->identified |= Constants\Id::PATTERN;
            }

            if (isset($this->data->device->model)) {
                $device = Data\DeviceModels::identify('s40', $this->data->device->model);
                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }

            if (isset($this->data->device->model)) {
                $device = Data\DeviceModels::identify('asha', $this->data->device->model);
                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->os->name = 'Nokia Asha Platform';
                    $this->data->os->version = new Version([ 'value' => '1.0' ]);
                    $this->data->device = $device;
                }

                if (preg_match('/java_runtime_version=Nokia_Asha_([0-9_]+);/u', $ua, $match)) {
                    $this->data->os->version = new Version([ 'value' => str_replace('_', '.', $match[1]) ]);
                }
            }

            $this->data->device->type = Constants\DeviceType::MOBILE;
        }

        /* Series 30+ */

        if (preg_match('/Series30Plus/u', $ua)) {
            $this->data->os->name = 'Series30+';

            if (preg_match('/Nokia([^\/]+)\//u', $ua, $match)) {
                $this->data->device->manufacturer = 'Nokia';
                $this->data->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->data->device->identified |= Constants\Id::PATTERN;
            }

            if (isset($this->data->device->model)) {
                $device = Data\DeviceModels::identify('s30plus', $this->data->device->model);
                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }

            $this->data->device->type = Constants\DeviceType::MOBILE;
        }

        /* Meego */

        if (preg_match('/MeeGo/u', $ua)) {
            $this->data->os->name = 'MeeGo';
            $this->data->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/Nokia([^\);]+)\)/u', $ua, $match)) {
                $this->data->device->manufacturer = 'Nokia';
                $this->data->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->data->device->identified |= Constants\Id::PATTERN;
                $this->data->device->generic = false;
            }
        }

        /* Maemo */

        if (preg_match('/Maemo/u', $ua)) {
            $this->data->os->name = 'Maemo';
            $this->data->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/(N[0-9]+)/u', $ua, $match)) {
                $this->data->device->manufacturer = 'Nokia';
                $this->data->device->model = $match[1];
                $this->data->device->identified |= Constants\Id::PATTERN;
                $this->data->device->generic = false;
            }
        }

        /* GEOS */

        if (preg_match('/Geos ([0-9.]+)/u', $ua, $match)) {
            $this->data->os->name = 'GEOS';
            $this->data->os->version = new Version([ 'value' => $match[1] ]);
            $this->data->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/Nokia-([0-9]{4,4}[a-z]?)/u', $ua, $match)) {
                $this->data->device->manufacturer = 'Nokia';
                $this->data->device->model = $match[1];
                $this->data->device->identified |= Constants\Id::PATTERN;
                $this->data->device->generic = false;
            }
        }
    }


    /* WebOS */

    private function detectWebos($ua)
    {
        if (preg_match('/(?:web|hpw)OS\/(?:HP webOS )?([0-9.]*)/u', $ua, $match)) {
            $this->data->os->name = 'webOS';
            $this->data->os->version = new Version([ 'value' => $match[1] ]);
            $this->data->device->type = preg_match('/Tablet/iu', $ua) ? Constants\DeviceType::TABLET : Constants\DeviceType::MOBILE;
            $this->data->device->generic = false;
        }

        if (preg_match('/(?:Spark|elite)\/fzz/u', $ua, $match) || preg_match('/webOSBrowser/u', $ua, $match)) {
            $this->data->os->name = 'webOS';
            $this->data->device->type = preg_match('/Tablet/iu', $ua) ? Constants\DeviceType::TABLET : Constants\DeviceType::MOBILE;
       