<?php
$rule = (object) array( 'id' => 901140, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'tx' => 
  array (
    'only' => 
    array (
      0 => 'critical_anomaly_score',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->set_var('tx.critical_anomaly_score','5');
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 901141, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'tx' => 
  array (
    'only' => 
    array (
      0 => 'error_anomaly_score',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->set_var('tx.error_anomaly_score','4');
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 901142, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'tx' => 
  array (
    'only' => 
    array (
      0 => 'warning_anomaly_score',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->set_var('tx.warning_anomaly_score','3');
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 901143, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'tx' => 
  array (
    'only' => 
    array (
      0 => 'notice_anomaly_score',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->set_var('tx.notice_anomaly_score','2');
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 901160, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'tx' => 
  array (
    'only' => 
    array (
      0 => 'allowed_methods',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->set_var('tx.allowed_methods','GET HEAD POST OPTIONS');
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$waf->set_var('tx.anomaly_score','0');
$waf->set_var('tx.anomaly_score_pl1','0');
$waf->set_var('tx.anomaly_score_pl2','0');
$waf->set_var('tx.anomaly_score_pl3','0');
$waf->set_var('tx.anomaly_score_pl4','0');
$waf->set_var('tx.sql_injection_score','0');
$waf->set_var('tx.xss_score','0');
$waf->set_var('tx.rfi_score','0');
$waf->set_var('tx.lfi_score','0');
$waf->set_var('tx.rce_score','0');
$waf->set_var('tx.php_injection_score','0');
$waf->set_var('tx.http_violation_score','0');
$waf->set_var('tx.session_fixation_score','0');
$waf->set_var('tx.inbound_anomaly_score','0');
$waf->set_var('tx.outbound_anomaly_score','0');
$waf->set_var('tx.outbound_anomaly_score_pl1','0');
$waf->set_var('tx.outbound_anomaly_score_pl2','0');
$waf->set_var('tx.outbound_anomaly_score_pl3','0');
$waf->set_var('tx.outbound_anomaly_score_pl4','0');
$waf->set_var('tx.sql_error_match','0');
$waf->set_var('tx.allowed_methods','GET HEAD POST OPTIONS PUT DELETE PATCH PURGE');
$rule = (object) array( 'id' => 4280017, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'begins_with','/is-admin/api/',false,false)) {
$waf->flag_rule_for_removal('id','932100');
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101301, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#3dprint.*tinyfilemanager\\.php$#Ds',false,false)) {
$rule->reason = '3DPrint-FileManager json payload blocked';
if($waf->match_targets(array (
),array (
  'request_method' => 
  array (
  ),
),'rx','#POST#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'content-type',
    ),
  ),
),'rx','#application/json#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110012, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#\\/wp-content\\/plugins\\/core-stab\\/index.*\\.php$#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'core-stab fake plugin direct access blocked';
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110013, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies_names' => 
  array (
    'only' => 
    array (
      0 => '/^\\d+$/',
    ),
    'count' => true,
  ),
),'ge','5',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'request for cookie based shell found and blocked';
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110016, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'Remote Code Execution attempt against WP User Post Gallary detected '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*action$/',
    ),
  ),
),'streq','upg_datatable',false,false)) {
if($waf->match_targets(array (
),array (
  'args' => 
  array (
  ),
),'rx','#field#Ds',false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 911100, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-generic',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272/220/274',
  7 => 'pci/12.1',
) );
try {
if($waf->match_targets(array (
),array (
  'request_method' => 
  array (
  ),
),'within',htmlentities($waf->get_var('tx.allowed_methods'), ENT_QUOTES, 'UTF-8') ,true,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Method is not allowed by policy '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 913100, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-reputation-scanner',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/118/224/541/310',
  7 => 'pci/6.5.10',
) );
try {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'user-agent',
    ),
  ),
),'pm',array (
  0 => '(hydra)',
  1 => '.nasl',
  2 => 'absinthe',
  3 => 'advanced email extractor',
  4 => 'arachni/',
  5 => 'autogetcontent',
  6 => 'bilbo',
  7 => 'BFAC',
  8 => 'brutus',
  9 => 'brutus/aet',
  10 => 'bsqlbf',
  11 => 'burpcollaborator',
  12 => 'cgichk',
  13 => 'cisco-torch',
  14 => 'commix',
  15 => 'core-project/1.0',
  16 => 'crimscanner/',
  17 => 'datacha0s',
  18 => 'Detectify',
  19 => 'dirbuster',
  20 => 'domino hunter',
  21 => 'dotdotpwn',
  22 => 'email extractor',
  23 => 'fhscan core 1.',
  24 => 'floodgate',
  25 => 'Fuzz Faster U Fool',
  26 => 'F-Secure Radar',
  27 => 'get-minimal',
  28 => 'gobuster',
  29 => 'gootkit auto-rooter scanner',
  30 => 'grabber',
  31 => 'grendel-scan',
  32 => 'havij',
  33 => 'httpx - Open-source project',
  34 => 'inspath',
  35 => 'internet ninja',
  36 => 'jaascois',
  37 => 'Jorgee',
  38 => 'masscan',
  39 => 'metis',
  40 => 'morfeus fucking scanner',
  41 => 'mysqloit',
  42 => 'n-stealth',
  43 => 'nessus',
  44 => 'netsparker',
  45 => 'nikto',
  46 => 'nmap nse',
  47 => 'nmap scripting engine',
  48 => 'nmap-nse',
  49 => 'nsauditor',
  50 => 'Nuclei',
  51 => 'openvas',
  52 => 'pangolin',
  53 => 'paros',
  54 => 'pmafind',
  55 => 'prog.customcrawler',
  56 => 'QQGameHall',
  57 => 'qualys was',
  58 => 's.t.a.l.k.e.r.',
  59 => 'security scan',
  60 => 'springenwerk',
  61 => 'sql power injector',
  62 => 'sqlmap',
  63 => 'sqlninja',
  64 => 'struts-pwn',
  65 => 'sysscan',
  66 => 'TBI-WebScanner',
  67 => 'teh forest lobster',
  68 => 'this is an exploit',
  69 => 'toata dragostea',
  70 => 'toata dragostea mea pentru diavola',
  71 => 'uil2pn',
  72 => 'user-agent:',
  73 => 'vega/',
  74 => 'voideye',
  75 => 'w3af.sf.net',
  76 => 'w3af.sourceforge.net',
  77 => 'w3af.org',
  78 => 'webbandit',
  79 => 'webinspect',
  80 => 'webshag',
  81 => 'webtrends security analyzer',
  82 => 'webvulnscan',
  83 => 'Wfuzz',
  84 => 'whatweb',
  85 => 'whcc/',
  86 => 'wordpress hash grabber',
  87 => 'WPScan',
  88 => 'xmlrpc exploit',
  89 => 'zgrab',
  90 => 'zmeu',
),false,true)) {
$rule->reason = 'Found User-Agent associated with security scanner Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'matched_vars' => 
  array (
  ),
),'rx','#^(?:urlgrabber/[0-9\\.]+ yum/[0-9\\.]+|mozilla/[0-9\\.]+ ecairn-grabber/[0-9\\.]+ \\(\\+http://ecairn.com/grabber\\))$#Ds',true,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->set_var('ip.reput_block_flag','1');
$waf->set_var('ip.reput_block_reason',$rule->reason);
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 913110, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-reputation-scanner',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/118/224/541/310',
  7 => 'pci/6.5.10',
) );
try {
if($waf->match_targets(array (
),array (
  'request_headers_names' => 
  array (
  ),
  'request_headers' => 
  array (
  ),
),'pm',array (
  0 => 'acunetix-product',
  1 => '(acunetix web vulnerability scanner',
  2 => 'acunetix-scanning-agreement',
  3 => 'acunetix-user-agreement',
  4 => 'myvar=1234',
  5 => 'x-ratproxy-loop',
  6 => 'bytes=0-,5-0,5-1,5-2,5-3,5-4,5-5,5-6,5-7,5-8,5-9,5-10,5-11,5-12,5-13,5-14',
  7 => 'x-scanner',
),false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->set_var('ip.reput_block_flag','1');
$waf->set_var('ip.reput_block_reason',$rule->reason);
$rule->reason = 'Found request header associated with security scanner Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 920100, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272',
) );
try {
if($waf->match_targets(array (
),array (
  'request_line' => 
  array (
  ),
),'rx','#(?i)^(?:(?:[a-z]{3,10}\\s+(?:\\w{3,7}?://[\\w\\-\\./]*(?::\\d+)?)?\\/[^?\\#]*(?:\\?[^\\#\\s]*)?(?:\\#[\\S]*)?|connect (?:(?:\\d{1,3}\\.){3}\\d{1,3}\\.?(?::\\d+)?|[\\w\\-\\./]+:\\d+)|options \\*)\\s+[\\w\\./]+|get \\/[^?\\#]*(?:\\?[^\\#\\s]*)?(?:\\#[\\S]*)?)$#Ds',true,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.warning_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Invalid HTTP Request Line '.$waf->meta('request_line');
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 920180, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272',
) );
try {
if($waf->match_targets(array (
),array (
  'request_protocol' => 
  array (
  ),
),'within','HTTP/2 HTTP/2.0',true,false)) {
$rule->reason = 'POST without Content-Length or Transfer-Encoding headers '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'request_method' => 
  array (
  ),
),'streq','POST',false,false)) {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'content-length',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'transfer-encoding',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.warning_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 920310, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272',
) );
try {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'accept',
    ),
  ),
),'rx','#^$#Ds',false,false)) {
$rule->reason = 'Request Has an Empty Accept Header';
if($waf->match_targets(array (
),array (
  'request_method' => 
  array (
  ),
),'rx','#^OPTIONS$#Ds',true,false)) {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'user-agent',
    ),
  ),
),'pm',array (
  0 => 'AppleWebKit',
  1 => 'Android',
  2 => 'Business',
  3 => 'Enterprise',
  4 => 'Entreprise',
),true,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.notice_anomaly_score'), ENT_QUOTES, 'UTF-8') );
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 920311, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272',
) );
try {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'accept',
    ),
  ),
),'rx','#^$#Ds',false,false)) {
$rule->reason = 'Request Has an Empty Accept Header';
if($waf->match_targets(array (
),array (
  'request_method' => 
  array (
  ),
),'rx','#^OPTIONS$#Ds',true,false)) {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'user-agent',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.notice_anomaly_score'), ENT_QUOTES, 'UTF-8') );
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 920330, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272',
) );
try {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'user-agent',
    ),
  ),
),'rx','#^$#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.notice_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Empty User Agent Header';
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 920340, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272',
) );
try {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'content-length',
    ),
  ),
),'rx','#^0$#Ds',true,false)) {
$rule->reason = 'Request Containing Content, but Missing Content-Type header';
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'content-type',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.notice_anomaly_score'), ENT_QUOTES, 'UTF-8') );
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 921140, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272/220/273',
) );
try {
if($waf->match_targets(array (
),array (
  'request_headers_names' => 
  array (
  ),
  'request_headers' => 
  array (
  ),
),'rx','#[\\n\\r]#Ds',false,true)) {
$waf->inc_var('tx.http_violation_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'HTTP Header Injection Attack via headers Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 932170, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-shell',
  2 => 'platform-unix',
  3 => 'attack-rce',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/88',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
  ),
  'request_line' => 
  array (
  ),
),'rx','#^\\(\\s*\\)\\s+{#Ds',false,true)) {
$waf->inc_var('tx.rce_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Remote Command Execution: Shellshock (CVE-2014-6271) Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$waf->set_var('tx.anomaly_score','0');
$waf->set_var('tx.anomaly_score_pl1','0');
$waf->set_var('tx.anomaly_score_pl2','0');
$waf->set_var('tx.anomaly_score_pl3','0');
$waf->set_var('tx.anomaly_score_pl4','0');
$waf->set_var('tx.sql_injection_score','0');
$waf->set_var('tx.xss_score','0');
$waf->set_var('tx.rfi_score','0');
$waf->set_var('tx.lfi_score','0');
$waf->set_var('tx.rce_score','0');
$waf->set_var('tx.php_injection_score','0');
$waf->set_var('tx.http_violation_score','0');
$waf->set_var('tx.session_fixation_score','0');
$waf->set_var('tx.inbound_anomaly_score','0');
$waf->set_var('tx.outbound_anomaly_score','0');
$waf->set_var('tx.outbound_anomaly_score_pl1','0');
$waf->set_var('tx.outbound_anomaly_score_pl2','0');
$waf->set_var('tx.outbound_anomaly_score_pl3','0');
$waf->set_var('tx.outbound_anomaly_score_pl4','0');
$waf->set_var('tx.sql_error_match','0');
$waf->set_var('tx.allowed_methods','GET HEAD POST OPTIONS PUT DELETE PATCH PURGE');
$rule = (object) array( 'id' => 99110008, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-post.php#Ds',false,false)) {
$rule->reason = 'Poll, Survey, Form & Quiz Maker XSS Attempt Detected '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'args_get' => 
  array (
    'only' => 
    array (
      0 => 'page',
    ),
  ),
),'rx','#opinionstage-content-login-callback-page#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'args_get' => 
  array (
  ),
),'rx','#(var\\s*u\\s*=|\\"?>.+)#Ds',false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110010, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'WooCommerce Currency Switcher LFI Attempt Detected '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => 'action',
    ),
  ),
),'streq','parse-media-shortcode',false,false)) {
if($waf->match_targets(array (
),array (
  'args_post' => 
  array (
    'only' => 
    array (
      0 => 'shortcode',
    ),
  ),
),'rx','#(\\[woocs\\s.*(?<=\\s)pagepath[^\\]]+\\])#Ds',false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110011, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = '3DPrint Lite Arbitrary File Upload detected '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*action$/',
    ),
  ),
),'streq','p3dlite_handle_upload',false,false)) {
if($waf->match_targets(array (
),array (
  'files' => 
  array (
  ),
),'rx','#\\.php#Ds',false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110009, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i:(union (?:all )?select (?:(?:1,)+concat\\(0x|null|unhex|0x6c6f67696e70776e7a,|char\\(45,120,49,45,81,45\\))))#Ds',false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Block MSSQL code execution and information gathering attempts '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101000, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'Potential Ninja Forms RCE';
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*action$/',
    ),
  ),
),'rx','#nf_ajax_submit#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'referer',
    ),
  ),
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*.wp.http.referer$/',
    ),
  ),
),'rx','#\\?.*::#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101001, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'Authentication bypass exploit attempt';
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*action$/',
    ),
  ),
),'rx','#^td_ajax_fb_login_(user|get_credentials)$#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101002, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'Authentication bypass exploit attempt';
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*action$/',
    ),
  ),
),'rx','#^ajax_save_options$#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'args_post' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*iubenda.section.name$/',
    ),
  ),
),'rx','#^(?!iubenda_)#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101003, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'Backdoor Upload Exploit Attempt';
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*action$/',
    ),
  ),
),'rx','#^user_registration_profile_pic_upload$#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'files' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*file$/',
    ),
  ),
),'rx','#\\.(ph(p|tml)[3-8]?|htaccess)$#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101004, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'Backdoor Upload Exploit Attempt';
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => 'action',
    ),
  ),
),'rx','#jb-upload-company-logo#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
  ),
),'rx','#\\.(ph(p|tml)[3-8]?|htaccess)$#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101005, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'Arbitrary Plugin Upload Attack Detected';
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*action$/',
    ),
  ),
),'rx','#^(stopbadbots|wpmemory|wptools|antihacker|cardealer)_install_plugin$#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'args_post' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*slug$/',
    ),
  ),
),'rx','#^(antihacker|stopbadbots|recaptcha-for-all|wp-memory|toolstruthsocial)$#Ds',true,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101300, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#3dprint.*tinyfilemanager\\.php$#Ds',false,false)) {
$rule->reason = '3DPrint-FileManager directory traversal attack';
if($waf->match_targets(array (
),array (
  'args_post_names' => 
  array (
  ),
),'rx','#(?i)group#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'args_post_names' => 
  array (
  ),
),'rx','#(?i)(delete|zip|tar)#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'args_post' => 
  array (
    'only' => 
    array (
      0 => 'file',
    ),
  ),
),'rx','#\\.\\.(\\\\|/)#Ds',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110014, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-json/wp/v2/users#Ds',false,false)) {
$rule->reason = 'Block WooCommerce-Payments Priv. Escalation';
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => '/^(?i)X-WCPAY-PLATFORM-CHECKOUT-USER$/',
    ),
  ),
),'rx','#^\\d+$#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => '/^(?i)Content-Type$/',
    ),
  ),
),'rx','#application/json#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'request_body' => 
  array (
  ),
),'contains','administrator',false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110015, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*rest.route$/',
    ),
  ),
  'request_filename' => 
  array (
  ),
),'rx','#(?i)(/wp-json/)?frm-admin/v1/install-addon#Ds',false,false)) {
$rule->reason = 'Block Formidable-Forms Arbitrary Plugin Install. Matched Data: '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*file.url$/',
    ),
  ),
  'request_cookies' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*file.url$/',
    ),
  ),
),'rx','#(?i)wordpress\\.org#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*file.url$/',
    ),
  ),
  'request_cookies' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*file.url$/',
    ),
  ),
),'rx','#^(?i)https://downloads\\.wordpress\\.org/plugin/((formidable-(gravity-forms-importer|import-pirate-forms))|wp-mail-smtp)\\.zip$#Ds',true,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99101302, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
),'rx','#/wp-admin/admin-ajax.php#Ds',false,false)) {
$rule->reason = 'Unsupported shortcode rendering detected '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'args' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*action$/',
    ),
  ),
),'streq','parse-media-shortcode',false,false)) {
if($waf->match_targets(array (
),array (
  'args_post' => 
  array (
    'only' => 
    array (
      0 => '/^\\s*shortcode$/',
    ),
  ),
),'rx','#(\\[(?!(audio|video|playlist)[\\s\\]])[^\\s\\]]*)#Ds',false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 99110017, 'reason' => '', 'tags' => array (
) );
try {
if($waf->match_targets(array (
),array (
  'args_post_names' => 
  array (
  ),
),'rx','#^\\s*um.request$#Ds',false,false)) {
$rule->reason = 'Privilege Escalation attack against Ultimate Member detected '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'args_post_names' => 
  array (
  ),
),'rx','#^\\s*.wpnonce$#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'args_post_names' => 
  array (
  ),
),'rx','#^\\s*form.id$#Ds',false,false)) {
if($waf->match_targets(array (
),array (
  'args_post_names' => 
  array (
  ),
),'rx','#\\[(?:administrator|editor|author|contributor)\\]$#Ds',false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 913120, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-reputation-scanner',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/118/224/541/310',
  7 => 'pci/6.5.10',
) );
try {
if($waf->match_targets(array (
),array (
  'request_filename' => 
  array (
  ),
  'args' => 
  array (
  ),
),'pm',array (
  0 => '/.adSensepostnottherenonobook',
  1 => '/<invalid>hello.html',
  2 => '/actSensepostnottherenonotive',
  3 => '/acunetix-wvs-test-for-some-inexistent-file',
  4 => '/antidisestablishmentarianism',
  5 => '/appscan_fingerprint/mac_address',
  6 => '/arachni-',
  7 => '/cybercop',
  8 => '/nessus_is_probing_you_',
  9 => '/nessustest',
  10 => '/netsparker-',
  11 => '/rfiinc.txt',
  12 => '/thereisnowaythat-you-canbethere',
  13 => '/w3af/remotefileinclude.html',
  14 => 'appscan_fingerprint',
  15 => 'w00tw00t.at.ISC.SANS.DFind',
  16 => 'w00tw00t.at.blackhats.romanian.anti-sec',
),false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->set_var('ip.reput_block_flag','1');
$waf->set_var('ip.reput_block_reason',$rule->reason);
$rule->reason = 'Found request filename/argument associated with security scanner Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 920270, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272',
) );
try {
if($waf->match_targets(array (
),array (
  'request_uri' => 
  array (
  ),
  'request_headers' => 
  array (
  ),
  'args' => 
  array (
  ),
  'args_names' => 
  array (
  ),
),'validate_byte_range',array (
  'min' => 1,
  'max' => 255,
  'range' => 
  array (
    0 => 
    array (
      0 => 1,
      1 => 255,
    ),
  ),
),false,false)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Invalid character in request (null character) '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .'='. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 921120, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272/220/34',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#[\\r\\n]\\W*?(?:content-(?:type|length)|set-cookie|location):\\s*\\w#Ds',false,true)) {
$waf->inc_var('tx.http_violation_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'HTTP Response Splitting Attack Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 921150, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-protocol',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/210/272/220/33',
) );
try {
if($waf->match_targets(array (
),array (
  'args_names' => 
  array (
  ),
),'rx','#[\\n\\r]#Ds',false,true)) {
$waf->inc_var('tx.http_violation_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'HTTP Header Injection Attack via payload (CR/LF detected) Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 930110, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-lfi',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/255/153/126',
) );
try {
if($waf->match_targets(array (
),array (
  'request_uri' => 
  array (
  ),
  'args' => 
  array (
  ),
  'request_headers' => 
  array (
    'except' => 
    array (
      0 => 'referer',
    ),
  ),
),'rx','#(?:(?:^|[\\\\\\\\/])\\.\\.[\\\\\\\\/]|[\\\\\\\\/]\\.\\.(?:[\\\\\\\\/]|$))#Ds',false,true)) {
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.lfi_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Path Traversal Attack (/../) Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 932160, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-shell',
  2 => 'platform-unix',
  3 => 'attack-rce',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/88',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'pm',array (
  0 => '${CDPATH}',
  1 => '${DIRSTACK}',
  2 => '${HOME}',
  3 => '${HOSTNAME}',
  4 => '${IFS}',
  5 => '${OLDPWD}',
  6 => '${OSTYPE}',
  7 => '${PATH}',
  8 => '${PWD}',
  9 => '$CDPATH',
  10 => '$DIRSTACK',
  11 => '$HOME',
  12 => '$HOSTNAME',
  13 => '$IFS',
  14 => '$OLDPWD',
  15 => '$OSTYPE',
  16 => '$PATH',
  17 => '$PWD',
  18 => 'bin/bash',
  19 => 'bin/cat',
  20 => 'bin/csh',
  21 => 'bin/dash',
  22 => 'bin/du',
  23 => 'bin/echo',
  24 => 'bin/grep',
  25 => 'bin/less',
  26 => 'bin/ls',
  27 => 'bin/mknod',
  28 => 'bin/more',
  29 => 'bin/nc',
  30 => 'bin/ps',
  31 => 'bin/rbash',
  32 => 'bin/sh',
  33 => 'bin/sleep',
  34 => 'bin/su',
  35 => 'bin/tcsh',
  36 => 'bin/uname',
  37 => 'dev/fd/',
  38 => 'dev/null',
  39 => 'dev/stderr',
  40 => 'dev/stdin',
  41 => 'dev/stdout',
  42 => 'dev/tcp/',
  43 => 'dev/udp/',
  44 => 'dev/zero',
  45 => 'etc/group',
  46 => 'etc/master.passwd',
  47 => 'etc/passwd',
  48 => 'etc/pwd.db',
  49 => 'etc/shadow',
  50 => 'etc/shells',
  51 => 'etc/spwd.db',
  52 => 'proc/self/',
  53 => 'usr/bin/awk',
  54 => 'usr/bin/base64',
  55 => 'usr/bin/cat',
  56 => 'usr/bin/cc',
  57 => 'usr/bin/clang',
  58 => 'usr/bin/clang++',
  59 => 'usr/bin/curl',
  60 => 'usr/bin/diff',
  61 => 'usr/bin/env',
  62 => 'usr/bin/fetch',
  63 => 'usr/bin/file',
  64 => 'usr/bin/find',
  65 => 'usr/bin/ftp',
  66 => 'usr/bin/gawk',
  67 => 'usr/bin/gcc',
  68 => 'usr/bin/head',
  69 => 'usr/bin/hexdump',
  70 => 'usr/bin/id',
  71 => 'usr/bin/less',
  72 => 'usr/bin/ln',
  73 => 'usr/bin/mkfifo',
  74 => 'usr/bin/more',
  75 => 'usr/bin/nc',
  76 => 'usr/bin/ncat',
  77 => 'usr/bin/nice',
  78 => 'usr/bin/nmap',
  79 => 'usr/bin/perl',
  80 => 'usr/bin/php',
  81 => 'usr/bin/php5',
  82 => 'usr/bin/php7',
  83 => 'usr/bin/php-cgi',
  84 => 'usr/bin/printf',
  85 => 'usr/bin/psed',
  86 => 'usr/bin/python',
  87 => 'usr/bin/python2',
  88 => 'usr/bin/python3',
  89 => 'usr/bin/ruby',
  90 => 'usr/bin/sed',
  91 => 'usr/bin/socat',
  92 => 'usr/bin/tail',
  93 => 'usr/bin/tee',
  94 => 'usr/bin/telnet',
  95 => 'usr/bin/top',
  96 => 'usr/bin/uname',
  97 => 'usr/bin/wget',
  98 => 'usr/bin/who',
  99 => 'usr/bin/whoami',
  100 => 'usr/bin/xargs',
  101 => 'usr/bin/xxd',
  102 => 'usr/bin/yes',
  103 => 'usr/local/bin/bash',
  104 => 'usr/local/bin/curl',
  105 => 'usr/local/bin/ncat',
  106 => 'usr/local/bin/nmap',
  107 => 'usr/local/bin/perl',
  108 => 'usr/local/bin/php',
  109 => 'usr/local/bin/python',
  110 => 'usr/local/bin/python2',
  111 => 'usr/local/bin/python3',
  112 => 'usr/local/bin/rbash',
  113 => 'usr/local/bin/ruby',
  114 => 'usr/local/bin/wget',
),false,true)) {
$waf->inc_var('tx.rce_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Remote Command Execution: Unix Shell Code Found Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 932180, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-rce',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/88',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'files' => 
  array (
  ),
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'x-filename',
      1 => 'x_filename',
      2 => 'x-file-name',
    ),
  ),
),'pm',array (
  0 => '.htaccess',
  1 => '.htdigest',
  2 => '.htpasswd',
  3 => 'wp-config.php',
  4 => 'config.yml',
  5 => 'config_dev.yml',
  6 => 'config_prod.yml',
  7 => 'config_test.yml',
  8 => 'parameters.yml',
  9 => 'routing.yml',
  10 => 'security.yml',
  11 => 'services.yml',
  12 => 'default.settings.php',
  13 => 'settings.php',
  14 => 'settings.local.php',
  15 => 'local.xml',
  16 => '.env',
),false,true)) {
$waf->inc_var('tx.rce_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Restricted File Upload Attempt Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 933110, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-php',
  2 => 'platform-multi',
  3 => 'attack-injection-php',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'files' => 
  array (
  ),
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'x-filename',
      1 => 'x_filename',
      2 => 'x.filename',
      3 => 'x-file-name',
    ),
  ),
),'rx','#.*\\.(?:php\\d*|phtml)\\.*$#Ds',false,true)) {
$waf->inc_var('tx.php_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'PHP Injection Attack: PHP Script File Upload Found Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 933130, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-php',
  2 => 'platform-multi',
  3 => 'attack-injection-php',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'pm',array (
  0 => '$GLOBALS',
  1 => '$HTTP_COOKIE_VARS',
  2 => '$HTTP_ENV_VARS',
  3 => '$HTTP_GET_VARS',
  4 => '$HTTP_POST_FILES',
  5 => '$HTTP_POST_VARS',
  6 => '$HTTP_RAW_POST_DATA',
  7 => '$HTTP_REQUEST_VARS',
  8 => '$HTTP_SERVER_VARS',
  9 => '$_COOKIE',
  10 => '$_ENV',
  11 => '$_FILES',
  12 => '$_GET',
  13 => '$_POST',
  14 => '$_REQUEST',
  15 => '$_SERVER',
  16 => '$_SESSION',
  17 => '$argc',
  18 => '$argv',
),false,true)) {
$waf->inc_var('tx.php_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'PHP Injection Attack: Variables Found Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 933140, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-php',
  2 => 'platform-multi',
  3 => 'attack-injection-php',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)php://(?:std(?:in|out|err)|(?:in|out)put|fd|memory|temp|filter)#Ds',false,true)) {
$waf->inc_var('tx.php_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'PHP Injection Attack: I/O Stream Found Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 933200, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-php',
  2 => 'platform-multi',
  3 => 'attack-injection-php',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i:zlib|glob|phar|ssh2|rar|ogg|expect|zip)://#Ds',false,false)) {
$waf->inc_var('tx.php_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'PHP Injection Attack: Wrapper scheme detected Matched Data: '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 933150, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-php',
  2 => 'platform-multi',
  3 => 'attack-injection-php',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_filename' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'pm',array (
  0 => '__halt_compiler',
  1 => 'apache_child_terminate',
  2 => 'base64_decode',
  3 => 'bzdecompress',
  4 => 'call_user_func',
  5 => 'call_user_func_array',
  6 => 'call_user_method',
  7 => 'call_user_method_array',
  8 => 'convert_uudecode',
  9 => 'file_get_contents',
  10 => 'file_put_contents',
  11 => 'fsockopen',
  12 => 'get_class_methods',
  13 => 'get_class_vars',
  14 => 'get_defined_constants',
  15 => 'get_defined_functions',
  16 => 'get_defined_vars',
  17 => 'gzdecode',
  18 => 'gzinflate',
  19 => 'gzuncompress',
  20 => 'include_once',
  21 => 'invokeargs',
  22 => 'pcntl_exec',
  23 => 'pcntl_fork',
  24 => 'pfsockopen',
  25 => 'posix_getcwd',
  26 => 'posix_getpwuid',
  27 => 'posix_getuid',
  28 => 'posix_uname',
  29 => 'ReflectionFunction',
  30 => 'require_once',
  31 => 'shell_exec',
  32 => 'str_rot13',
  33 => 'sys_get_temp_dir',
  34 => 'wp_remote_fopen',
  35 => 'wp_remote_get',
  36 => 'wp_remote_head',
  37 => 'wp_remote_post',
  38 => 'wp_remote_request',
  39 => 'wp_safe_remote_get',
  40 => 'wp_safe_remote_head',
  41 => 'wp_safe_remote_post',
  42 => 'wp_safe_remote_request',
  43 => 'zlib_decode',
),false,true)) {
$waf->inc_var('tx.php_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'PHP Injection Attack: High-Risk PHP Function Name Found Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 933170, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-php',
  2 => 'platform-multi',
  3 => 'attack-injection-php',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_headers' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#[oOcC]:\\d+:\\".+?\\":\\d+:{.*}#Ds',false,true)) {
$waf->inc_var('tx.php_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'PHP Injection Attack: Serialized Object Injection Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 934100, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-javascript',
  2 => 'platform-multi',
  3 => 'attack-rce',
  4 => 'attack-injection-nodejs',
  5 => 'paranoia-level/1',
  6 => 'owasp_crs',
  7 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?:(?:_(?:\\$\\$ND_FUNC\\$\\$_|_js_function)|(?:new\\s+Function|\\beval)\\s*\\(|String\\s*\\.\\s*fromCharCode|function\\s*\\(\\s*\\)\\s*{|this\\.constructor)|module\\.exports\\s*=)#Ds',false,true)) {
$waf->inc_var('tx.rce_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Node.js Injection Attack Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 941110, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-xss',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_filename' => 
  array (
  ),
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'user-agent',
      1 => 'referer',
    ),
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)<script[^>]*>[\\s\\S]*?#Ds',false,true)) {
$waf->inc_var('tx.xss_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'XSS Filter - Category 1: Script Tag Vector Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 941140, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-xss',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'user-agent',
      1 => 'referer',
    ),
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)[a-z]+=(?:[^:=]+:.+;)*?[^:=]+:url\\(javascript#Ds',false,true)) {
$waf->inc_var('tx.xss_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'XSS Filter - Category 4: Javascript URI Vector Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 941170, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-xss',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'user-agent',
      1 => 'referer',
    ),
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)(?:\\W|^)(?:javascript:(?:[\\s\\S]+[=\\x5c\\(\\[\\.<]|[\\s\\S]*?(?:\\bname\\b|\\x5c[ux]\\d))|data:(?:(?:[a-z]\\w+/\\w[\\w+-]+\\w)?[;,]|[\\s\\S]*?;[\\s\\S]*?\\b(?:base64|charset=)|[\\s\\S]*?,[\\s\\S]*?<[\\s\\S]*?\\w[\\s\\S]*?>))|@\\W*?i\\W*?m\\W*?p\\W*?o\\W*?r\\W*?t\\W*?(?:/\\*[\\s\\S]*?)?(?:[\\"\']|\\W*?u\\W*?r\\W*?l[\\s\\S]*?\\()|[^-]*?-\\W*?m\\W*?o\\W*?z\\W*?-\\W*?b\\W*?i\\W*?n\\W*?d\\W*?i\\W*?n\\W*?g[^:]*?:\\W*?u\\W*?r\\W*?l[\\s\\S]*?\\(#Ds',false,true)) {
$waf->inc_var('tx.xss_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'NoScript XSS InjectionChecker: Attribute Injection Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 941210, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-xss',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i:(?:j|&\\#x?0*(?:74|4A|106|6A);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:a|&\\#x?0*(?:65|41|97|61);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:v|&\\#x?0*(?:86|56|118|76);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:a|&\\#x?0*(?:65|41|97|61);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:s|&\\#x?0*(?:83|53|115|73);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:c|&\\#x?0*(?:67|43|99|63);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:r|&\\#x?0*(?:82|52|114|72);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:i|&\\#x?0*(?:73|49|105|69);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:p|&\\#x?0*(?:80|50|112|70);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?:t|&\\#x?0*(?:84|54|116|74);?)(?:\\t|&(?:\\#x?0*(?:9|13|10|A|D);?|tab;|newline;))*(?::|&(?:\\#x?0*(?:58|3A);?|colon;)).)#Ds',false,true)) {
$waf->inc_var('tx.xss_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'IE XSS Filters - Attack Detected Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 941240, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-xss',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#<[?]?import[\\s/+\\S]*?implementation[\\s/+]*?=#Ds',false,true)) {
$waf->inc_var('tx.xss_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'IE XSS Filters - Attack Detected Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 941120, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-xss',
  4 => 'paranoia-level/2',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/242',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'user-agent',
      1 => 'referer',
    ),
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)[\\s\\"\'`;\\/0-9=\\x0B\\x09\\x0C\\x3B\\x2C\\x28\\x3B]on[a-zA-Z]{3,25}[\\s\\x0B\\x09\\x0C\\x3B\\x2C\\x28\\x3B]*?=[^=]#Ds',false,true)) {
$waf->inc_var('tx.xss_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl2',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'XSS Filter - Category 2: Event Handler Vector Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942160, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
) );
try {
if($waf->match_targets(array (
),array (
  'request_basename' => 
  array (
  ),
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i:sleep\\(\\s*?\\d*?\\s*?\\)|benchmark\\(.*?\\,.*?\\))#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Detects blind sqli tests using sleep() or benchmark() Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942170, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)(?:select|;)\\s+(?:benchmark|sleep|if)\\s*?\\(\\s*?\\(?\\s*?\\w+#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Detects SQL benchmark and sleep injection attempts including conditional queries Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942220, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#^(?i:-0000023456|4294967295|4294967296|2147483648|2147483647|0000012345|-2147483648|-2147483649|0000023456|2.2250738585072007e-308|2.2250738585072011e-308|1e309)$#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Looking for integer overflow attacks, these are taken from skipfish, except 2.2.2250738585072011e-308 is the \\"magic number\\" crash Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942240, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)(?:[\\"\'`](?:;*?\\s*?waitfor\\s+(?:delay|time)\\s+[\\"\'`]|;.*?:\\s*?goto)|alter\\s*?\\w+.*?cha(?:racte)?r\\s+set\\s+\\w+)#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Detects MySQL charset switch and MSSQL DoS attempts Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942280, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)(?:;\\s*?shutdown\\s*?(?:[\\#;{]|\\/\\*|--)|waitfor\\s*?delay\\s?[\\"\'`]+\\s?\\d|select\\s*?pg_sleep)#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Detects Postgres pg_sleep injection, waitfor delay attacks and database shutdown attempts Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942290, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i:(?:\\[\\$(?:ne|eq|lte?|gte?|n?in|mod|all|size|exists|type|slice|x?or|div|like|between|and)\\]))#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Finds basic MongoDB SQL injection attempts Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942320, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)(?:create\\s+(?:procedure|function)\\s*?\\w+\\s*?\\(\\s*?\\)\\s*?-|;\\s*?(?:declare|open)\\s+[\\w-]+|procedure\\s+analyse\\s*?\\(|declare[^\\w]+[@\\#]\\s*?\\w+|exec\\s*?\\(\\s*?@)#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Detects MySQL and PostgreSQL stored procedure/function injections Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942350, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i)(?:;\\s*?(?:(?:(?:trunc|cre|upd)at|renam)e|d(?:e(?:lete|sc)|rop)|(?:inser|selec)t|alter|load)\\b\\s*?[\\[(]?\\w{2,}|create\\s+function\\s.+\\sreturns)#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Detects MySQL UDF injection and other data/structure manipulation attempts Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 942500, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-sqli',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/152/248/66',
  7 => 'pci/6.5.2',
) );
try {
if($waf->match_targets(array (
),array (
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'args' => 
  array (
  ),
),'rx','#(?i:/\\*[!+](?:[\\w\\s=_\\-()]+)?\\*/)#Ds',false,true)) {
$waf->inc_var('tx.sql_injection_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'MySQL in-line comment detected Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 943110, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-fixation',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/225/21/593/61',
) );
try {
if($waf->match_targets(array (
),array (
  'args_names' => 
  array (
  ),
),'rx','#^(?:jsessionid|aspsessionid|asp\\.net_sessionid|phpsession|phpsessid|weblogicsession|session_id|session-id|cfid|cftoken|cfsid|jservsession|jwsession)$#Ds',false,true)) {
$rule->reason = 'Possible Session Fixation Attack: SessionID Parameter Name with Off-Domain Referer Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'referer',
    ),
  ),
),'rx','#^(?:ht|f)tps?://(.*?)/#Ds',false,true)) {
if($waf->match_targets(array (
),array (
  'tx' => 
  array (
    'only' => 
    array (
      0 => '1',
    ),
  ),
),'ends_with','%{request_headers.host}',true,false)) {
$waf->inc_var('tx.session_fixation_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 943120, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-multi',
  2 => 'platform-multi',
  3 => 'attack-fixation',
  4 => 'paranoia-level/1',
  5 => 'owasp_crs',
  6 => 'capec/1000/225/21/593/61',
) );
try {
if($waf->match_targets(array (
),array (
  'args_names' => 
  array (
  ),
),'rx','#^(?:jsessionid|aspsessionid|asp\\.net_sessionid|phpsession|phpsessid|weblogicsession|session_id|session-id|cfid|cftoken|cfsid|jservsession|jwsession)$#Ds',false,true)) {
$rule->reason = 'Possible Session Fixation Attack: SessionID Parameter Name with No Referer Matched Data: '.htmlentities($waf->get_var('tx.0'), ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') .': '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'request_headers' => 
  array (
    'only' => 
    array (
      0 => 'referer',
    ),
    'count' => true,
  ),
),'eq','0',false,false)) {
$waf->inc_var('tx.session_fixation_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 944100, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-java',
  2 => 'platform-multi',
  3 => 'attack-rce',
  4 => 'owasp_crs',
  5 => 'capec/1000/152/137/6',
  6 => 'pci/6.5.2',
  7 => 'paranoia-level/1',
) );
try {
if($waf->match_targets(array (
),array (
  'args' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_body' => 
  array (
  ),
  'request_headers' => 
  array (
  ),
),'rx','#java\\.lang\\.(?:runtime|processbuilder)#Ds',false,false)) {
$waf->inc_var('tx.rce_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Remote Command Execution: Suspicious Java class detected Matched Data: '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 944120, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-java',
  2 => 'platform-multi',
  3 => 'attack-rce',
  4 => 'owasp_crs',
  5 => 'capec/1000/152/248',
  6 => 'pci/6.5.2',
  7 => 'paranoia-level/1',
) );
try {
if($waf->match_targets(array (
),array (
  'args' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_body' => 
  array (
  ),
  'request_headers' => 
  array (
  ),
),'rx','#(?:clonetransformer|forclosure|instantiatefactory|instantiatetransformer|invokertransformer|prototypeclonefactory|prototypeserializationfactory|whileclosure|getproperty|filewriter|xmldecoder)#Ds',false,false)) {
$rule->reason = 'Remote Command Execution: Java serialization (CVE-2015-4852) Matched Data: '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') ;
if($waf->match_targets(array (
),array (
  'matched_vars' => 
  array (
  ),
),'rx','#(?:runtime|processbuilder)#Ds',false,false)) {
$waf->inc_var('tx.rce_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
return $waf->block('block',$rule->id,$rule->reason,403);
}
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}
$rule = (object) array( 'id' => 944130, 'reason' => '', 'tags' => array (
  0 => 'application-multi',
  1 => 'language-java',
  2 => 'platform-multi',
  3 => 'attack-rce',
  4 => 'owasp_crs',
  5 => 'capec/1000/152/248',
  6 => 'pci/6.5.2',
  7 => 'paranoia-level/1',
) );
try {
if($waf->match_targets(array (
),array (
  'args' => 
  array (
  ),
  'args_names' => 
  array (
  ),
  'request_cookies' => 
  array (
    'except' => 
    array (
      0 => '/__utm/',
    ),
  ),
  'request_cookies_names' => 
  array (
  ),
  'request_body' => 
  array (
  ),
  'request_filename' => 
  array (
  ),
  'request_headers' => 
  array (
  ),
),'pm',array (
  0 => 'com.opensymphony.xwork2',
  1 => 'com.sun.org.apache',
  2 => 'java.io.BufferedInputStream',
  3 => 'java.io.BufferedReader',
  4 => 'java.io.ByteArrayInputStream',
  5 => 'java.io.ByteArrayOutputStream',
  6 => 'java.io.CharArrayReader',
  7 => 'java.io.DataInputStream',
  8 => 'java.io.File',
  9 => 'java.io.FileOutputStream',
  10 => 'java.io.FilePermission',
  11 => 'java.io.FileWriter',
  12 => 'java.io.FilterInputStream',
  13 => 'java.io.FilterOutputStream',
  14 => 'java.io.FilterReader',
  15 => 'java.io.InputStream',
  16 => 'java.io.InputStreamReader',
  17 => 'java.io.LineNumberReader',
  18 => 'java.io.ObjectOutputStream',
  19 => 'java.io.OutputStream',
  20 => 'java.io.PipedOutputStream',
  21 => 'java.io.PipedReader',
  22 => 'java.io.PrintStream',
  23 => 'java.io.PushbackInputStream',
  24 => 'java.io.Reader',
  25 => 'java.io.StringReader',
  26 => 'java.lang.Class',
  27 => 'java.lang.Integer',
  28 => 'java.lang.Number',
  29 => 'java.lang.Object',
  30 => 'java.lang.Process',
  31 => 'java.lang.ProcessBuilder',
  32 => 'java.lang.reflect',
  33 => 'java.lang.Runtime',
  34 => 'java.lang.String',
  35 => 'java.lang.StringBuilder',
  36 => 'java.lang.System',
  37 => 'javax.script.ScriptEngineManager',
  38 => 'org.apache.commons',
  39 => 'org.apache.struts',
  40 => 'org.apache.struts2',
  41 => 'org.omg.CORBA',
  42 => 'java.beans.XMLDecode',
),false,false)) {
$waf->inc_var('tx.rce_score',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$waf->inc_var('tx.anomaly_score_pl1',htmlentities($waf->get_var('tx.critical_anomaly_score'), ENT_QUOTES, 'UTF-8') );
$rule->reason = 'Suspicious Java class detected Matched Data: '. htmlentities($waf->matched_var, ENT_QUOTES, 'UTF-8') .' found within '. htmlentities($waf->matched_var_name, ENT_QUOTES, 'UTF-8') ;
return $waf->block('block',$rule->id,$rule->reason,403);
}
} catch ( \Throwable $t ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $t );
} catch ( \Exception $e ) {
error_log( 'Rule ' . $rule->id . ' failed: ' . $e );
}