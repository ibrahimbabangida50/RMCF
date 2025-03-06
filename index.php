<?php
// Include your database connection file
require_once 'db.php';

// Start session to handle language preferences
session_start();
// Get the session variables
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];

// Split the text into an array
$input = explode('*', $text);
$level = count($input);

// Set default language
$lang = 'kiny';

// Check if language is set to English
if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') {
    $lang = 'en';
}

function handleRegistration($level, $input, $phoneNumber, $pdo, $lang) {
    // Check if user is already registered
    $stmt = $pdo->prepare("SELECT name FROM Users WHERE phone = ?");
    $stmt->execute([$phoneNumber]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo ($lang == 'en') ? 
            "END You have already registered. Thank you, {$user['name']}." :
            "END Mwamaze kwiyandikisha. Murakoze, {$user['name']}.";
        return;
    }

    if ($level == 2) {
        echo ($lang == 'en') ?
            "CON Choose Registration Type\n1. Individual\n2. Family\n3. Institution\n4. Organization" :
            "CON Hitamo Uwiyandikisha\n1. Umuntu ku giti cye\n2. Umuryango\n3. Ikigo\n4. Ishyirahamwe";
    } elseif ($level == 3) {
        echo ($lang == 'en') ? "CON Enter registrant's name" : "CON Injiza izina ry'uwiyandikisha";
    } elseif ($level == 4) {
        echo ($lang == 'en') ? "CON Choose your district" : "CON Akarere";
    } elseif ($level == 5) {
        echo ($lang == 'en') ?
            "CON Choose Contribution Class\n1. Class 1\n2. Class 2" :
            "CON Hitamo Ikiciro cy'umusanzu\n1. Ikiciro 1\n2. Ikiciro 2";
    } elseif ($level == 6) {
        $class = $input[5];
        if ($class == '1') {
            echo ($lang == 'en') ?
                "CON Choose amount\n1. 100000\n2. 50000\n3. 30000\n4. 20000" :
                "CON Hitamo amafaranga\n1. 100000\n2. 50000\n3. 30000\n4. 20000";
        } elseif ($class == '2') {
            echo ($lang == 'en') ?
                "CON Choose amount\n1. 10000\n2. 5000\n3. 3000\n4. 2000\n5. 1000" :
                "CON Hitamo amafaranga\n1. 10000\n2. 5000\n3. 3000\n4. 2000\n5. 1000";
        } else {
            echo ($lang == 'en') ? "END Invalid choice." : "END Ibyo mwahisemo sibyo mwongere mugerageze.";
        }
    } elseif ($level == 7) {
        $amountOptionsClass1 = ['1' => 100000, '2' => 50000, '3' => 30000, '4' => 20000];
        $amountOptionsClass2 = ['1' => 10000, '2' => 5000, '3' => 3000, '4' => 2000, '5' => 1000];

        $selectedAmount = $input[6];
        $class = $input[5];
        $amount = ($class == '1') ? $amountOptionsClass1[$selectedAmount] ?? null : $amountOptionsClass2[$selectedAmount] ?? null;

        if ($amount) {
            try {
                // Save user to the database
                $stmt = $pdo->prepare("INSERT INTO Users (phone, name, registration_type, contribution_class, monthly_amount, district) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$phoneNumber, $input[3], $input[2], $class, $amount, $input[4]]);
                
                echo ($lang == 'en') ?
                    "END Registration successful. Thank you {$input[3]}." :
                    "END Kwiyandikisha byagenze neza. Murakoze {$input[3]}.";
            } catch (PDOException $e) {
                error_log("Registration failed: " . $e->getMessage());
                echo ($lang == 'en') ?
                    "END An error occurred. Please try again." :
                    "END Hari ikibazo cyabaye. Mwongere mugerageze.";
            }
        } else {
            echo ($lang == 'en') ? "END Invalid choice." : "END Ibyomwahisemo siyo mwongere mugerageze.";
        }
    }
}

function handleContribution($level, $input, $phoneNumber, $pdo, $lang) {
    if ($level == 2) {
        echo ($lang == 'en') ?
            "CON Pay Contribution\n1. Process Payment\n2. Sadaqh\n3. Zakat\n4. Return to Main Menu" : 
            "CON Tanga kubikowra bikurikira \n1.Umusanzu w'ukwezi Quran na Dawa\n2. Kubaka Umusigiti w'Itunda \n3. Zakat\n4. Subira Inyuma";
    } elseif ($level == 3) {
        switch ($input[2]) {
            case '1':
                processPayment($phoneNumber, $pdo, $lang);
                break;
            case '2':
                echo ($lang == 'en') ? "CON Enter the amount of Sadaqh you wish to give" : "CON Umubare wa Sadaq ushaka Gutanga";
                break;
            case '3':
                echo ($lang == 'en') ? "CON Choose Zakat type\n1. Zakat al-Fitr\n2. Zakat al-Mal" : "CON Hitamo ubwoko bwa Zakat\n1. Zakat al-Fitr\n2. Zakat al-Mal";
                break;
            case '4':
                returnToMainMenu($lang);
                break;
            default:
                echo ($lang == 'en') ? "END Invalid choice." : "END Ibyo mwahisemo sibyo mwongere mugerageze.";
                break;
        }
    } elseif ($level == 4 && $input[2] == '2') {
        saveSadaqh($input[3], $phoneNumber, $pdo, $lang);
        
    } elseif ($level == 4 && $input[2] == '3' && $input[3] == '1') {
        echo ($lang == 'en') ? "CON Enter the number of people for Zakat al-Fitr" : "CON Andika umubare wabo wifuriza kwishyurira Zakat al-Fitr";
    } elseif ($level == 5 && $input[2] == '3' && $input[3] == '1') {
        $numPeople = (int)$input[4];
        $totalAmount = $numPeople * 2500;
        echo ($lang == 'en') ? 
            "CON Total amount for Zakat al-Fitr is {$totalAmount}. Press 1 to Confirm" : 
            "CON Amafaranga yose ni {$totalAmount}. Kanda 1 wemeze Kwishyura";
    } elseif ($level == 6 && $input[2] == '3' && $input[3] == '1' && $input[5] == '1') {
        try {
            $stmt = $pdo->prepare("SELECT name, district FROM Users WHERE phone = ?");
            $stmt->execute([$phoneNumber]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $donorName = $user['name'];
                $district = $user['district'];
                $numPeople = (int)$input[4];
                $totalAmount = $numPeople * 2500;

                // Save Zakat transaction
                $stmt = $pdo->prepare("INSERT INTO Zakat (donor_name, phone, district, Zakat_type, amount, donation_date) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$donorName, $phoneNumber, $district, 'Zakat al-Fitr', $totalAmount]);

                echo ($lang == 'en') ? 
                    "END Your Zakat al-Fitr payment of {$totalAmount} has been received. Thank you, {$donorName}. You will receive an SMS shortly." : 
                    "END Ubusabe bwanyu bwo kwishyura {$totalAmount} Bwakiriwe. Murakoze, {$donorName}. Murabona Ubutumwa bugufi.";

                // Execute additional USSD call for Zakat al-Fitr
                shell_exec("*182*1*1*0788965797*{$totalAmount}#");
            } else {
                echo ($lang == 'en') ? "END User not found. Please register first." : "END Ntabwo mwiyandikishije.Mwiyandikishe mbere.";
            }
        } catch (Exception $e) {
            error_log("Zakat al-Fitr processing failed: " . $e->getMessage());
            echo ($lang == 'en') ? "END An error occurred while processing your Zakat al-Fitr. Please try again later." : "END Hari ikibazo cyabaye mu gutanga Zakat al-Fitr. Mwongere mukagerageze.";
        }
    } elseif ($level == 4 && $input[2] == '3' && $input[3] == '2') {  
        echo ($lang == 'en') ? "CON Enter the total wealth amount you want to pay Zakat on:" : 
            "CON Umubare w'ubutunzi bwose ushaka gutangira Zakat al-Mal";
    } elseif ($level == 5 && $input[2] == '3' && $input[3] == '2') {
        $wealthAmount = (float)$input[4];  
        $zakatAmount = $wealthAmount * 0.025;  

        echo ($lang == 'en') ? 
            "CON Your Zakat al-Mal amount is {$zakatAmount} RWF. Press 1 to Confirm Payment" : 
            "CON Amafaranga ya Zakat al-Mal ni {$zakatAmount} RWF. Kanda 1 Emeza Kwishyura";
    } elseif ($level == 6 && $input[2] == '3' && $input[3] == '2' && $input[5] == '1') {
        try {
            $stmt = $pdo->prepare("SELECT name, district FROM Users WHERE phone = ?");
            $stmt->execute([$phoneNumber]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $donorName = $user['name'];
                $district = $user['district'];
                $wealthAmount = (float)$input[4];
                $zakatAmount = $wealthAmount * 0.025;

                // Save Zakat transaction
                $stmt = $pdo->prepare("INSERT INTO Zakat (donor_name, phone, district, Zakat_type, amount, donation_date) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$donorName, $phoneNumber, $district,'Zakat al-Mal', $zakatAmount]);

                // Execute additional USSD call for Zakat al-Mal
                //shell_exec("*182*1*1*0788965797*{$zakatAmount}#");

                echo ($lang == 'en') ? 
                    "END Your Zakat al-Mal payment of {$zakatAmount} RWF has been recorded. Thank you, {$donorName}. You will receive an SMS shortly." : 
                    "END Ubusabe bwanyu bwo kwishyura {$zakatAmount} RWF Zakat al-Mal Bwakiriwe. Murakoze, {$donorName}. Murabona Ubutumwa bugufi.";
            } else {
                echo ($lang == 'en') ? "END User not found. Please register first." : "END Ntabwo mwiyandikishije.Mwiyandikishe mbere.";
            }
        } catch (Exception $e) {
            error_log("Zakat al-Mal processing failed: " . $e->getMessage());
            echo ($lang == 'en') ? "END An error occurred while processing your Zakat al-Mal. Please try again later." : 
                "END Hari ikibazo cyabaye mu gutanga Zakat al-Mal. Mwongere mukagerageze.";
        }
    }
}

function processPayment($phoneNumber, $pdo, $lang) {
    try {
        $stmt = $pdo->prepare("SELECT monthly_amount FROM Users WHERE phone = ?");
        $stmt->execute([$phoneNumber]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $amount = $user['monthly_amount'];

            // Record transaction in database
            $stmt = $pdo->prepare("INSERT INTO Contributions (phone, amount, payment_date) VALUES (?, ?, CURRENT_DATE())");
            $stmt->execute([$phoneNumber, $amount]);

            if ($stmt->rowCount() > 0) {
                echo ($lang == 'en') ?
                    "END Your payment of {$amount} has been recorded. Thank you for your contribution." : 
                    "END Ubwishyu bwawe bwa {$amount} bwanditswe. Murakoze ku musanzu wanyu.";
                
                // Execute additional USSD call for Contributions
                //shell_exec("*182*1*1*0788965797*{$amount}#");
            } else {
                throw new Exception("Failed to record the transaction.");
            }
        } else {
            echo ($lang == 'en') ?
                "END User not found. Please register first." : 
                "END Ntabwo mwiyandikishije.Mwiyandikishe mbere..";
        }
    } catch (Exception $e) {
        error_log("Payment processing failed: " . $e->getMessage());
        echo ($lang == 'en') ?
            "END An error occurred while processing your payment. Please try again later." : 
            "END Hari ikibazo cyabaye mu gihe cyo gutunganya ubwishyu bwawe. Nyamuneka mwongere mugerageze.";
    }
}

function saveSadaqh($amount, $phoneNumber, $pdo, $lang) {
    try {
        // Validate if amount is numeric
        if (!is_numeric($amount)) {
            echo ($lang == 'en') ? "END Invalid amount entered." : "END Ibyo mwanditse si byo mwongere mugerageze.";
            return;
        }

        // Fetch user information from the Users table
        $stmt = $pdo->prepare("SELECT name, district FROM Users WHERE phone = ?");
        $stmt->execute([$phoneNumber]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo ($lang == 'en') ? "END User not found. Please register first." : "END Ntabwo mwiyandikishije.Mwiyandikishe mbere.";
            return;
        }

        // Insert Sadaqh into the database
        $stmt = $pdo->prepare("INSERT INTO Sadaqh (donor_name, phone, district, amount, donation_date) VALUES (?, ?, ?, ?, CURRENT_DATE())");
        $stmt->execute([$user['name'], $phoneNumber, $user['district'], $amount]);

        // Fetch the total amount given in Sadaqh by all users
        $stmt = $pdo->prepare("SELECT SUM(amount) AS total_sadaqh FROM Sadaqh");
        $stmt->execute();
        $totalSadaqh = $stmt->fetchColumn();

        echo ($lang == 'en') ?
            "END Your request to give Sadaqh of {$amount} has been received. Total Sadaqh by all users: {$totalSadaqh}. Thank you, {$user['name']}." :
            "END Ubusab bwanyu bwo gutanga Sadaq bwa {$amount} bwakiriwe. Amafranga amaze gutangwa: {$totalSadaqh}. Murakoze, {$user['name']}.";

        // Execute additional USSD call for Sadaqah
       // shell_exec("*182*1*1*0788965797*{$amount}#");

    } catch (Exception $e) {
        error_log("Sadaqh processing failed: " . $e->getMessage());
        echo ($lang == 'en') ? "END An error occurred while processing your Sadaqh. Please try again later." : "END Hari ikibazo cyabaye mu gutanga Sadaq. Mwongere mukagerageze.";
    }
}

function handleAccountCheck($phoneNumber, $pdo, $lang) {
    $stmt = $pdo->prepare("SELECT name, contribution_class, monthly_amount FROM Users WHERE phone = ?");
    $stmt->execute([$phoneNumber]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $stmt = $pdo->prepare("SELECT SUM(amount) AS total_paid FROM Contributions WHERE phone = ?");
        $stmt->execute([$phoneNumber]);
        $totalPaid = $stmt->fetchColumn() ?: 0;

        $unpaidMonths = calculateUnpaidMonths($user['monthly_amount'], $totalPaid);

        echo ($lang == 'en') ?
            "END Your Account: \nName: {$user['name']}\nClass: {$user['contribution_class']}\nAmount Paid: {$totalPaid}\nRemaining Amount: {$unpaidMonths}" :
            "END Konte yawe: \nIzina: {$user['name']}\nIcyiciro: {$user['contribution_class']}\nAmafaranga yishyuye: {$totalPaid}\nAmafaranga asigaye: {$unpaidMonths}";
    } else {
        echo ($lang == 'en') ? "END You are not registered." : "END Ntabwo mwiyandikishije.Mwiyandikishe mbere.";
    }
}

function handleLanguageChange($level, $input) {
    if ($level == 2) {
        echo "CON Choose Language\n1. English\n2. Kinyarwanda";
    } elseif ($level == 3) {
        if ($input[2] == '1') {
            $_SESSION['lang'] = 'en';
            echo "CON Welcome to the development of Islam\n1. Register\n2. Pay Contribution\n3. Check Your Account\n4. Change Language";
        } elseif ($input[2] == '2') {
            $_SESSION['lang'] = 'kiny';
            echo "CON Murakaza neza mu iterambere rya Islam\n1. Kwiyandikisha\n2. Ishyura Umusanzu\n3. Kureba konte yawe\n4. Guhindura ururimi";
        } else {
            echo "END Invalid choice.";
        }
    }
}

function calculateUnpaidMonths($monthlyAmount, $totalPaid) {
    // Implement this function based on your business logic
    return $monthlyAmount - $totalPaid;
}

function returnToMainMenu($lang) {
    echo ($lang == 'en') ?
        "CON Welcome to the development of Islam\n1. Register\n2. Pay Contribution\n3. Check Your Account\n4. Change Language" :
        "CON Murakaza neza mu iterambere rya Islam\n1. Kwiyandikisha\n2. Gutanga \n3. Kureba konte yawe\n4. Guhindura ururimi";
}

// Main menu logic
if ($level == 1) {
    // Main menu
    echo ($lang == 'en') ? 
        "CON Welcome to the development of Islam\n1. Register\n2. Pay Contribution\n3. Check Your Account\n4. Change Language" :
        "CON Murakaza neza mu iterambere rya Islam\n1. Kwiyandikisha\n2. Gutanga \n3. Kureba konte yawe\n4. Guhindura ururimi";
} elseif ($level >= 2) {
    switch ($input[1]) {
        case '1':  
            // Registration menu
            handleRegistration($level, $input, $phoneNumber, $pdo, $lang);
            break;
        case '2':  
            // Contribution payment menu
            handleContribution($level, $input, $phoneNumber, $pdo, $lang);
            break;
        case '3':  
            // Check account
            handleAccountCheck($phoneNumber, $pdo, $lang);
            break;
        case '4':  
            // Language change menu
            handleLanguageChange($level, $input);
            break;
        default:  
            echo ($lang == 'en') ? "END Invalid choice." : "END Ibyo mwahisemo sibyo mwongere mugerageze.";
    }
} else {
    echo ($lang == 'en') ? "END Invalid choice." : "END Ibyo mwahisemo sibyo mwongere mugerageze.";
}

?>
