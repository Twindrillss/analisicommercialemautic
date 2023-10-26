<?php


        function aggiungizero($dato){
         if ($dato == 1){
        $pulito = '01';
    }else if ($dato == 2){
        $pulito = '02';
    }else if ($dato == 3){
        $pulito = '03';
    }else if ($dato == 4){
        $pulito = '04';
    }else if ($dato == 5){
        $pulito = '05';
    }else if ($dato == 6){
        $pulito = '06';
    }else if ($dato == 7){
        $pulito = '07';
    }else if ($dato == 8){
        $pulito = '08';
    }else if ($dato == 9){
        $pulito = '09';
    } else {
        $pulito = $dato;
    }
    return $pulito;
    }
class Calendar {

    private $active_year, $active_month, $active_day;
    private $events = [];

    public function __construct($date = null) {
        $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
        $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
        $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
    }

    public function add_event($txt, $date, $days = 1, $color = '',$lead,$note,$numeronota = 0) {
        $color = $color ? ' ' . $color : $color;
        $this->events[] = [$txt, $date, $days, $color,$lead,$note,$numeronota];
    }
    

    public function __toString() {
        $num_days = date('t', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year));
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year)));
        $days = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
        $first_day_of_week = array_search(date('D', strtotime($this->active_year . '-' . $this->active_month . '-1')), $days);
        $html = '<div class="calendar">';
        $html .= '<div class="header">';
        $html .= '<div class="month-year">';
        $html .= date('F Y', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day));
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="days">';
        foreach ($days as $day) {
            $html .= '
                <div class="day_name">
                    ' . $day . '
                </div>
            ';
        }
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '
                <div class="day_num ignore">
                    ' . ($num_days_last_month-$i+1) . '
                </div>
            ';
        }
        for ($i = 1; $i <= $num_days; $i++) {
            $selected = '';
            $idselected = '';
            if ($i == $this->active_day) {
                $selected = ' selected';
                $idselected = 'id="giornocorrente"';
            }
            $html .= '<div class="day_num' . $selected . '"'.$idselected.'>';
            $html .= '<span>' . $i . '</span>';
            foreach ($this->events as $event) {
                for ($d = 0; $d <= ($event[2]-1); $d++) {
                    if (date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day')) == date('y-m-d', strtotime($event[1]))) {
    
    $datafix = date_parse($event[1]);
    
    $giornopulito = aggiungizero($datafix["day"]);
    $mesepulito = aggiungizero($datafix["month"]);
    
    $datacompletahtml5 = $datafix["year"].'-'.$mesepulito.'-'.$giornopulito;
    
    
    $rimuovi = array("'");
$nomecognomefix = str_replace($rimuovi, "", $event[0]);
    
   
    

                        $html .= '<div class="event tooltip' . $event[3] . '">';
                        $html .= '<a class="nomecognomecalendario" target="_blank" style="color:white;" onclick="boxazioni'."('".$nomecognomefix."','".$event[5]."','http://gestionale.360forma.com/s/contacts/view/".$event[4]."'".",'".$event[6]."'".",'".$datacompletahtml5."'".')">';
                        $html .= '<span class="tooltiptext">Nota: '.$event[5].'</span>';
                        $html .= $event[0];
                        $html .= '</a>';
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
        }
        for ($i = 1; $i <= (42-$num_days-max($first_day_of_week, 0)); $i++) {
            $html .= '
                <div class="day_num ignore">
                    ' . $i . '
                </div>
            ';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

}
?>