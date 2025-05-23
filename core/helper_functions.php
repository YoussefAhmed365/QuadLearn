<?php
// helper_functions.php
function time_elapsed_string($datetime, $full = false)
{
  // Ensure the time zone is set correctly
  date_default_timezone_set('Africa/Cairo'); // Replace 'Your/Timezone' with your server's time zone like 'Asia/Baghdad'

  $now = new DateTime();
  $ago = new DateTime($datetime);
  $diff = $now->diff($ago);

  // If the difference is less than a minute, return "الآن"
  if ($diff->y === 0 && $diff->m === 0 && $diff->d === 0 && $diff->h === 0 && $diff->i === 0) {
    return 'الآن';
  }

  $string = [
    'y' => 'سنة',
    'm' => 'شهر',
    'd' => 'يوم',
    'h' => 'ساعة',
    'i' => 'دقيقة',
    's' => 'ثانية',
  ];
  $plural = [
    'y' => 'سنوات',
    'm' => 'أشهر',
    'd' => 'أيام',
    'h' => 'ساعات',
    'i' => 'دقائق',
    's' => 'ثواني',
  ];

  $result = [];
  foreach ($string as $key => $value) {
    if ($diff->$key > 0) {
      $result[] = 'منذ ' . $diff->$key . ' ' . ($diff->$key > 1 ? $plural[$key] : $value);
    }
  }

  if (!$full) {
    $result = array_slice($result, 0, 1);
  }

  return $result ? implode(', ', $result) : 'الآن';
}

function formatCreatedAt($createdAt)
{
  $now = new DateTime();
  $createdAtDate = new DateTime($createdAt);
  $interval = $now->diff($createdAtDate);

  // المصفوفات المساعدة
  $months = [
    'Jan' => 'يناير',
    'Feb' => 'فبراير',
    'Mar' => 'مارس',
    'Apr' => 'أبريل',
    'May' => 'مايو',
    'Jun' => 'يونيو',
    'Jul' => 'يوليو',
    'Aug' => 'أغسطس',
    'Sep' => 'سبتمبر',
    'Oct' => 'أكتوبر',
    'Nov' => 'نوفمبر',
    'Dec' => 'ديسمبر',
  ];

  // إذا كان التاريخ في نفس اليوم
  if ($interval->d === 0 && $interval->m === 0 && $interval->y === 0) {
    if ($interval->h === 0) {
      if ($interval->i === 0) {
        return "الآن";
      }
      return "منذ {$interval->i} دقيقة";
    }
    if ($interval->h === 1) {
      return "منذ ساعة";
    }
    if ($interval->h <= 10) {
      return "منذ {$interval->h} ساعات";
    }
    return $createdAtDate->format('g:i A');
  }

  // إذا كان التاريخ بالأمس
  if ($interval->days === 1) {
    return "أمس الساعة " . $createdAtDate->format('g:i A');
  }

  // إذا كان خلال الأسبوع
  if ($interval->days < 7) {
    return "منذ {$interval->days} أيام";
  }

  // إذا كان خلال السنة الحالية
  if ($interval->y === 0) {
    $day = $createdAtDate->format('d');
    $monthAr = $months[$createdAtDate->format('M')];
    return "$day $monthAr";
  }

  // إذا كان أكثر من سنة
  return $createdAtDate->format('d/m/Y');
}