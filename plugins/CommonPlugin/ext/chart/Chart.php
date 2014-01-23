<?php

class Chart {
       
        private static $_first = true;
        private static $_count = 0;
       
        private $_chartType;
       
        private $_data;
        private $_dataType;
        private $_skipFirstRow;
       
        /**
         * sets the chart type and updates the chart counter
         */
        public function __construct($chartType, $skipFirstRow = false){
                $this->_chartType = $chartType;
                $this->_skipFirstRow = $skipFirstRow;
                self::$_count++;
        }
       
        /**
         * loads the dataset and converts it to the correct format
         */
        public function load($data, $dataType = 'json'){
                $this->_data = ($dataType != 'json') ? $this->dataToJson($data) : $data;
        }
       
        /**
         * load jsapi
         */
        private function initChart(){
                self::$_first = false;
               
                $output = '';
                // start a code block
                $output .= '<script type="text/javascript" src="https://www.google.com/jsapi"></script>'."\n";
                $output .= '<script type="text/javascript">google.load(\'visualization\', \'1.0\', {\'packages\':[\'corechart\']});</script>'."\n";
               
                return $output;
        }
       
        /**
         * draws the chart
         */
       
        public function draw($div, Array $options = array()){
                $output = '';
               
                if(self::$_first)$output .= $this->initChart();
               
                // start a code block
                $output .= '<script type="text/javascript">';

                // set callback function
                $output .= 'google.setOnLoadCallback(drawChart' . self::$_count . ');';
               
                // create callback function
                $output .= 'function drawChart' . self::$_count . '(){';
               
                $output .= 'var data = new google.visualization.DataTable(' . $this->_data . ');';
               
                // set the options
                $output .= 'var options = ' . json_encode($options) . ';';
               
                // create and draw the chart
                $output .= 'var chart = new google.visualization.' . $this->_chartType . '(document.getElementById(\'' . $div . '\'));';
                $output .= 'chart.draw(data, options);';
               
                $output .= '} </script>' . "\n";
                return $output;
        }
               
        /**
         * substracts the column names from the first and second row in the dataset
         */
        private function getColumns($data){
                $cols = array();
                foreach($data[0] as $key => $value){
                        if(is_numeric($key)){
                                if(is_string($data[1][$key])){
                                        $cols[] = array('id' => '', 'label' => $value, 'type' => 'string');
                                } else {
                                        $cols[] = array('id' => '', 'label' => $value, 'type' => 'number');
                                }
                                $this->_skipFirstRow = true;
                        } else {
                                if(is_string($value)){
                                        $cols[] = array('id' => '', 'label' => $key, 'type' => 'string');
                                } else {
                                        $cols[] = array('id' => '', 'label' => $key, 'type' => 'number');
                                }
                        }
                }
                return $cols;
        }
       
        /**
         * convert array data to json
         * info: http://code.google.com/intl/nl-NL/apis/chart/interactive/docs/datatables_dataviews.html#javascriptliteral
         */
        private function dataToJson($data){
                $cols = $this->getColumns($data);
               
                $rows = array();
                foreach($data as $key => $row){
                        if($key != 0 || !$this->_skipFirstRow){
                                $c = array();
                                foreach($row as $v){
                                        $c[] = array('v' => $v);
                                }
                                $rows[] = array('c' => $c);
                        }
                }
               
                return json_encode(array('cols' => $cols, 'rows' => $rows));
        }
       
}
