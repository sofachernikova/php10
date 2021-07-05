<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <link rel='stylesheet' href='style.css'>
    <title>Калькулятор, Черникова Софья, 201-321</title>
</head>
<header>
    <p>
        Черникова Софья Кирилловна 201-321, <a href="https://github.com/sofachernikova/php10.git">ссылка на гитхаб</a>
    </p>
</header>

<body>
    <main>
        <?php
        session_start();
        if (!isset($_SESSION['history'])) {
            $_SESSION['history'] = array();
            $_SESSION['iteration'] = 0;
        } else $_SESSION['iteration'] += 1;

        require 'form.html';

        if (isset($_POST['val'])) {
            $res = calculateSq($_POST['val']); // вычисляем результат выражения
            if (isnum($res)) // если полученный результат является числом
                echo '<div class="success">Значение выражения: ' . $_POST['val'] . ' = ' . $res . '</div>'; // вывод значения
            else // если результат не число – значит ошибка!
                echo '<div class="success">Ошибка вычисления выражения: ' . $_POST['val'] . '  ' . $res . '</div>'; // вывод ошибки
        }

        function isCalcable($x)
        {
            if (preg_match('/(\d+\.\d+|\d+)[+*\/\-:]/', $x)) return true;
            else return false;
        }

        function isnum($x)
        {
            if (@$x[0] == '.') return false;
            for ($i = 0, $dotCount = false; $i < strlen($x); $i++) {
                switch (true) {
                    case (@$x[$i] == '.'):
                        if ($dotCount) return false;
                        $dotCount = true;
                        break;
                    case (preg_match('/[a-zA-Z_]/', $x)):
                        return false;
                    default:
                        return true;
                }
            }
        }

        function SqValidator($value)
        {
            $open = 0; // создаем счетчик открывающихся скобок
            $value = preg_replace('/\s+/', '', $value);
            for ($i = 0; $i < strlen($value); $i++) // перебираем все символы строки
            {
                if ($value[$i] == '(') // если встретилась «(»
                    $open++; // увеличиваем счетчик
                else
            if ($value[$i] == ')') // если встретилась «)»
                {
                    $open--; // уменьшаем счетчик
                    if ($open < 0) // если найдена «)» без соответствующей «(»
                        return false; // возвращаем ошибку
                }
            }
            // если количество открывающихся и закрывающихся скобок разное
            if ($open !== 0) return false; // возвращаем ошибку
            if ($open === 0) return true; // количество скобок совпадает – все ОК
        }

        function calculateSq($val)
        {
            $val = preg_replace('/\s+/', '', $val);
            if (!SqValidator($val)) echo 'Расставьте скобки правильно';
            $start = strpos($val, '(');
            if ($start === false) return calculate($val);
            for ($end = $start + 1; $end < strlen($val); $end++) {
                if ($val[$end] == '(') $start = $end;
                if ($val[$end] == ')') break;
            }
            $new_val = substr($val, 0, $start);
            $new_val .= substr($val, ($start + 1), $end - $start - 1);
            $new_val .= substr($val, $end + 1);
            return calculateSq($new_val);
        }

        function calculate($val)
        {
            if (!$val) return 'Выражение не задано';
            if (!isCalcable($val)) return $val;



            $args = explode('*', $val);
            if (count($args) > 1) {
                $comp = 1;
                for ($i = 0; $i < count($args); $i++) {
                    switch (true) {
                        case (isCalcable($args[$i])):
                            $comp *= calculate($args[$i]);
                            break;
                        case (isnum($args[$i])):
                            $comp *= $args[$i];
                            break;
                        default:
                            'Операнд не числовой';
                            break;
                    }
                }

                return $comp;
            }

            $args = explode(':', $val);
            if (count($args) > 1) return divide($args);
            $args = explode('/', $val);
            if (count($args) > 1) return divide($args);

            $args = explode('+', $val);
            if (count($args) > 1) {
                $sum = 0;
                for ($i = 0; $i < count($args); $i++) {
                    switch (true) {
                        case (isCalcable($args[$i])):
                            $sum += calculate($args[$i]);
                            break;
                        case (isnum($args[$i])):
                            $sum += $args[$i];
                            break;

                        default:
                            'Операнд не числовой';
                            break;
                    }
                }
                return $sum;
            }

            $args = explode('-', $val);

            if (count($args) > 1) {
                switch (true) {
                    case (isCalcable($args[0])):
                        $sub = calculate($args[0]);
                        break;
                    case (isnum($args[0])):
                        $sub = $args[0];
                        break;
                    default:
                        'Операнд не числовой';
                        break;
                }

                for ($i = 1; $i < count($args); $i++) {
                    switch (true) {
                        case (isCalcable($args[$i])):
                            $args[$i] = calculate($args[$i]);
                            $sub = $sub - $args[$i];
                            break;
                        case (isnum($args[$i])):
                            $sub = $sub - $args[$i];
                            break;

                        default:
                            'Операнд не числовой';
                            break;
                    }
                }
                return $sub;
            }
        }

        function divide($args)
        {
            switch (true) {
                case (isCalcable($args[0])):
                    $div = calculate($args[0]);
                    break;
                case (isnum($args[0])):
                    $div = $args[0];
                    break;
                default:
                    'Операнд не числовой';
                    break;
            }

            for ($i = 1; $i < count($args); $i++) {
                switch (true) {
                    case (isCalcable($args[$i])):
                        $args[$i] = calculate($args[$i]);
                        $sub = $div - $args[$i];
                        break;
                    case (isnum($args[$i])):
                        $div = $div / $args[$i];
                        break;

                    default:
                        'Операнд не числовой';
                        break;
                }
            }
            return $div;
        }
        for ($i = 0; $i < count($_SESSION['history']); $i++) {
            echo $_SESSION['history'][$i] . '<br>';
        }

        if (isset($_POST['val']) && $_POST['iteration'] + 1 == $_SESSION['iteration'])
            $_SESSION['history'][] = $_POST['val'] . ' = ' . $res;

        echo '</div>';
        ?>
    </main>
</body>

<footer>
    <img src='img\Mospolytech_logo.jpg'>
</footer>

</html>