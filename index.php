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

    if ($level == 1) {
        echo ($lang == 'en') ?
            "CON Choose Registration Type\n1. Individual\n2. Family\n3. Institution\n4. Organization" :
            "CON Hitamo Uwiyandikisha\n1. Umuntu ku giti cye\n2. Umuryango\n3. Ikigo\n4. Ishyirahamwe";
    } elseif ($level == 2) {
        // Note: Registration type is now in $input[1] (not $input[2])
        echo ($lang == 'en') ? "CON Enter registrant's name" : "CON Injiza izina ry'uwiyandikisha";
    } elseif ($level == 3) {
        // Name is in $input[2], district selection prompt
        echo ($lang == 'en') ? "CON Choose your district" : "CON Akarere";
    } elseif ($level == 4) {
        // District is in $input[3], class selection prompt
        echo ($lang == 'en') ?
            "CON Choose Contribution Class\n1. Class 1\n2. Class 2" :
            "CON Hitamo Ikiciro cy'umusanzu\n1. Ikiciro 1\n2. Ikiciro 2";
    } elseif ($level == 5) {
        // Class is in $input[4] (not $input[5])
        $class = $input[4] ?? null;
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
    } elseif ($level == 6) {
        $amountOptionsClass1 = ['1' => 100000, '2' => 50000, '3' => 30000, '4' => 20000];
        $amountOptionsClass2 = ['1' => 10000, '2' => 5000, '3' => 3000, '4' => 2000, '5' => 1000];

        // Amount selection is in $input[5] (not $input[6])
        $selectedAmount = $input[5] ?? null;
        $class = $input[4] ?? null;
        $amount = ($class == '1') ? ($amountOptionsClass1[$selectedAmount] ?? null) : ($amountOptionsClass2[$selectedAmount] ?? null);

        if ($amount) {
            try {
                // Save user to the database
                // Note: input indices are now 2 (name), 1 (type), 4 (class), 3 (district)
                $stmt = $pdo->prepare("INSERT INTO Users (phone, name, registration_type, contribution_class, monthly_amount, district) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $phoneNumber, 
                    $input[2] ?? '',    // Name
                    $input[1] ?? '',    // Registration type
                    $class,
                    $amount,
                    $input[3] ?? ''     // District
                ]);
                
                echo ($lang == 'en') ?
                    "END Registration successful. Thank you {$input[2]}." :
                    "END Kwiyandikisha byagenze neza. Murakoze {$input[2]}.";
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
    if ($level == 1) {
        echo ($lang == 'en') ?
            "CON Pay Contribution\n1. Process Payment\n2. Sadaqh\n3. Zakat\n4. Sadaqh k Umusigiti\n5. Return to Main Menu" : 
            "CON Tanga kubikorwa bikurikira \n1. Umusanzu w'ukwezi Quran na Dawa\n2. Kubaka Umusigiti w'Itunda \n3. Zakat\n4. Sadaqh k Umusigiti\n5. Subira Inyuma";
    } 
    elseif ($level == 2) {
        switch ($input[1]) {
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
                echo ($lang == 'en') ? "CON Enter Masjid Number" : "CON Andika numero y'Umusigiti";
                break;
            case '5':
                returnToMainMenu($lang);
                break;
            default:
                echo ($lang == 'en') ? "END Invalid choice." : "END Ibyo mwahisemo sibyo mwongere mugerageze.";
                break;
        }
    }
    elseif ($level == 3 && $input[1] == '2') {  // Changed from $input[2] to $input[1]
        saveSadaqh($input[2], $phoneNumber, $pdo, $lang);  // Changed from $input[3] to $input[2]
    } 
    elseif ($level == 3 && $input[1] == '3' && $input[2] == '1') {  // Adjusted indices
        echo ($lang == 'en') ? "CON Enter the number of people for Zakat al-Fitr" : "CON Andika umubare wabo wifuriza kwishyurira Zakat al-Fitr";
    } 
    elseif ($level == 4 && $input[1] == '3' && $input[2] == '1') {  // Adjusted indices
        $numPeople = (int)($input[3] ?? 0);  // Changed from $input[4] to $input[3]
        $totalAmount = $numPeople * 4000;
        echo ($lang == 'en') ? 
            "CON Total amount for Zakat al-Fitr is {$totalAmount}. Press 1 to Confirm" : 
            "CON Amafaranga yose ni {$totalAmount}. Kanda 1 wemeze Kwishyura";
    } 
    elseif ($level == 5 && $input[1] == '3' && $input[2] == '1' && $input[4] == '1') {  // Adjusted indices
        try {
            $stmt = $pdo->prepare("SELECT name, district FROM Users WHERE phone = ?");
            $stmt->execute([$phoneNumber]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $donorName = $user['name'];
                $district = $user['district'];
                $numPeople = (int)($input[3] ?? 0);  // Changed from $input[4] to $input[3]
                $totalAmount = $numPeople * 4000;

                $stmt = $pdo->prepare("INSERT INTO Zakat (donor_name, phone, district, Zakat_type, amount, donation_date) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$donorName, $phoneNumber, $district, 'Zakat al-Fitr', $totalAmount]);

                echo ($lang == 'en') ? 
                    "END Your Zakat al-Fitr payment of {$totalAmount} has been received. Thank you, {$donorName}. You will receive an SMS shortly." : 
                    "END Ubusabe bwanyu bwo kwishyura {$totalAmount} Bwakiriwe. Murakoze, {$donorName}. Murabona Ubutumwa bugufi.";

                shell_exec("*182*1*1*0788965797*{$totalAmount}#");
            } else {
                echo ($lang == 'en') ? "END User not found. Please register first." : "END Ntabwo mwiyandikishije.Mwiyandikishe mbere.";
            }
        } catch (Exception $e) {
            error_log("Zakat al-Fitr processing failed: " . $e->getMessage());
            echo ($lang == 'en') ? "END An error occurred while processing your Zakat al-Fitr. Please try again later." : "END Hari ikibazo cyabaye mu gutanga Zakat al-Fitr. Mwongere mukagerageze.";
        }
    
    } 
    elseif ($level == 3 && $input[1] == '3' && $input[2] == '2') {  // Adjusted indices  
        echo ($lang == 'en') ? "CON Enter the total wealth amount you want to pay Zakat on:" : 
            "CON Umubare w'ubutunzi bwose ushaka gutangira Zakat al-Mal";

    } 
    elseif ($level == 4 && $input[1] == '3' && $input[2] == '2') {  // Adjusted indices
        $wealthAmount = (float)($input[3] ?? 0);  // Changed from $input[4] to $input[3]
        $zakatAmount = $wealthAmount * 0.025;  

        echo ($lang == 'en') ? 
            "CON Your Zakat al-Mal amount is {$zakatAmount} RWF. Press 1 to Confirm Payment" : 
            "CON Amafaranga ya Zakat al-Mal ni {$zakatAmount} RWF. Kanda 1 Emeza Kwishyura";
    } 
    elseif ($level == 5 && $input[1] == '3' && $input[2] == '2' && $input[4] == '1') {  // Adjusted indices
        try {
            $stmt = $pdo->prepare("SELECT name, district FROM Users WHERE phone = ?");
            $stmt->execute([$phoneNumber]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $donorName = $user['name'];
                $district = $user['district'];
                $wealthAmount = (float)($input[3] ?? 0);  // Changed from $input[4] to $input[3]
                $zakatAmount = $wealthAmount * 0.025;

                $stmt = $pdo->prepare("INSERT INTO Zakat (donor_name, phone, district, Zakat_type, amount, donation_date) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$donorName, $phoneNumber, $district,'Zakat al-Mal', $zakatAmount]);

                shell_exec("*182*1*1*0788965797*{$zakatAmount}#");

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
    // Add new cases for Sadaqh k Umusigiti
    elseif ($level == 3 && $input[1] == '4') {
        // User entered masjid number, now verify and show masjid name
        $masjidNumber = $input[2] ?? '';
        
        try {
            // Get masjid details
            $stmt = $pdo->prepare("SELECT masjid_name, district FROM masjid WHERE masjid_number = ?");
            $stmt->execute([$masjidNumber]);
            $masjid = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$masjid) {
                echo ($lang == 'en') ? "END Masjid not found. Please try again." : "END Ntabwo Umusigiti wabonetse. Mwongere mugerageze.";
                return;
            }
            
            // Show masjid name and ask for amount
            echo ($lang == 'en') ? 
                "CON Enter amount you wish to give to {$masjid['masjid_name']} in {$masjid['district']}" : 
                "CON Andika umubare wa Sadaq ushaka gutanga kuri {$masjid['masjid_name']}  Akarere ka {$masjid['district']}";
                
            // Store masjid number in session for next step
            $_SESSION['current_masjid_number'] = $masjidNumber;
            
        } catch (Exception $e) {
            error_log("Masjid lookup failed: " . $e->getMessage());
            echo ($lang == 'en') ? 
                "END An error occurred while looking up masjid. Please try again later." : 
                "END Hari ikibazo cyabaye mu gushakisha Umusigiti. Mwongere mukagerageze.";
        }
    }
    elseif ($level == 4 && $input[1] == '4') {
        // Process the Sadaqh for Masjid
        $masjidNumber = $_SESSION['current_masjid_number'] ?? '';
        $amount = $input[3] ?? 0;
        
        try {
            // Get masjid details again (in case session was lost)
            if (empty($masjidNumber)) {
                $masjidNumber = $input[2] ?? '';
            }
            
            $stmt = $pdo->prepare("SELECT * FROM masjid WHERE masjid_number = ?");
            $stmt->execute([$masjidNumber]);
            $masjid = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$masjid) {
                echo ($lang == 'en') ? "END Masjid not found." : "END Ntabwo Umusigiti wabonetse.";
                return;
            }
            
            // Get user details
            $stmt = $pdo->prepare("SELECT name, district FROM Users WHERE phone = ?");
            $stmt->execute([$phoneNumber]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                echo ($lang == 'en') ? "END User not found. Please register first." : "END Ntabwo mwiyandikishije.Mwiyandikishe mbere.";
                return;
            }
            
            // Save to Sadaqhformasjid table
            $stmt = $pdo->prepare("INSERT INTO Sadaqhformasjid 
                (donor_name, phone, donor_district, amount, masjid_number, masjid_name, 
                 masjid_district, masjid_sector, donation_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending')");
            $stmt->execute([
                $user['name'],
                $phoneNumber,
                $user['district'],
                $amount,
                $masjidNumber,
                $masjid['masjid_name'],
                $masjid['district'],
                $masjid['sector']
            ]);
            
            // Send confirmation message
            echo ($lang == 'en') ? 
                "END Your request to pay {$amount} to {$masjid['masjid_name']} has been received. You shall see a message soon. Thank you, {$user['name']}." : 
                "END Ubusabe bwanyu bwo kwishyura {$amount} kuri {$masjid['masjid_name']} bwakiriwe. Murabona Ubutumwa bugufi. Murakoze, {$user['name']}.";
            
            // Execute USSD payment command
            shell_exec("*182*1*1*0788965797*{$amount}#");
            
            // Clear the session variable
            unset($_SESSION['current_masjid_number']);
            
        } catch (Exception $e) {
            error_log("Sadaqh for Masjid processing failed: " . $e->getMessage());
            echo ($lang == 'en') ? 
                "END An error occurred while processing your Sadaqh for Masjid. Please try again later." : 
                "END Hari ikibazo cyabaye mu gutanga Sadaq y'Umusigiti. Mwongere mukagerageze.";
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
                shell_exec("*182*1*1*0788965797*{$amount}#");
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
        shell_exec("*182*1*1*0788965797*{$amount}#");

    } catch (Exception $e) {
        error_log("Sadaqh processing failed: " . $e->getMessage());
        echo ($lang == 'en') ? "END An error occurred while processing your Sadaqh. Please try again later." : "END Hari ikibazo cyabaye mu gutanga Sadaq. Mwongere mukagerageze.";
    }
}

function handleAccountCheck($level, $input, $phoneNumber, $pdo, $lang) {
    try {
        // Level 1 - Show account check options
        if ($level == 1) {
            echo ($lang == 'en') 
                ? "CON Account Check\n1. Your Account\n2. Masjid Account" 
                : "CON Kureba Konti\n1. Konti yawe\n2. Konti y'Umusigiti";
            return;
        }

        // Level 2 - Handle menu selection
        if ($level == 2) {
            $choice = $input[1] ?? '';
            
            if ($choice == '1') {
                // Check user account (unchanged)
                $stmt = $pdo->prepare("SELECT name, contribution_class, monthly_amount FROM Users WHERE phone = ?");
                $stmt->execute([$phoneNumber]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $stmt = $pdo->prepare("SELECT SUM(amount) FROM Contributions WHERE phone = ?");
                    $stmt->execute([$phoneNumber]);
                    $totalPaid = $stmt->fetchColumn() ?: 0;

                    $unpaid = max(0, $user['monthly_amount'] - $totalPaid);
                    
                    echo ($lang == 'en')
                        ? "END Your Account:\nName: {$user['name']}\nClass: {$user['contribution_class']}\nTotal Paid: {$totalPaid}\nUnpaid: {$unpaid}"
                        : "END Konti yawe:\nIzina: {$user['name']}\nIcyiciro: {$user['contribution_class']}\nYishyuwe: {$totalPaid}\nAtishyuwe: {$unpaid}";
                } else {
                    echo ($lang == 'en') 
                        ? "END You are not registered." 
                        : "END Ntabwo mwiyandikishije.";
                }
                return;
            } 
            elseif ($choice == '2') {
                echo ($lang == 'en') 
                    ? "CON Enter Masjid Passcode:" 
                    : "CON Andika passcode y'Umusigiti:";
                return;
            }
            else {
                echo ($lang == 'en') 
                    ? "END Invalid choice." 
                    : "END Ibyo mwahisemo sibyo.";
                return;
            }
        }

        // Level 3 - Handle passcode input and show report
        if ($level == 3) {
            $passcode = trim($input[2] ?? '');
            
            if (empty($passcode)) {
                echo ($lang == 'en') 
                    ? "END Passcode required." 
                    : "END Passcode irakenewe.";
                return;
            }

            // Get masjid details using passcode
            $stmt = $pdo->prepare("SELECT m.masjid_name, m.district 
                                 FROM masjid m
                                 JOIN Sadaqhformasjid s ON m.passcode = s.passcode
                                 WHERE m.passcode = ?
                                 LIMIT 1");
            $stmt->execute([$passcode]);
            $masjid = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$masjid) {
                echo ($lang == 'en') 
                    ? "END Masjid not found with this passcode." 
                    : "END Ntabwo Umusigiti wabonetse na passcode iyo.";
                return;
            }

            // Get masjid account summary using passcode
            $stmt = $pdo->prepare("SELECT 
                                    SUM(amount) as total_amount,
                                    COUNT(*) as transactions,
                                    MAX(donation_date) as last_donation
                                  FROM Sadaqhformasjid 
                                  WHERE passcode = ?");
            $stmt->execute([$passcode]);
            $report = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total = $report['total_amount'] ? number_format($report['total_amount']) : '0';
            $count = $report['transactions'] ?? '0';
            $last = $report['last_donation'] ? date('d/m/Y', strtotime($report['last_donation'])) : 
                  ($lang == 'en' ? 'Never' : 'Nta bushyinguro');
            
            if ($lang == 'en') {
                $response = "END Masjid Account Report\n";
                $response .= "Name: {$masjid['masjid_name']}\n";
                $response .= "District: {$masjid['district']}\n";
                $response .= "Total Received: {$total} RWF\n";
                $response .= "Transactions: {$count}\n";
                $response .= "Last Donation: {$last}";
            } else {
                $response = "END Raporo y'Konti y'Umusigiti\n";
                $response .= "Izina: {$masjid['masjid_name']}\n";
                $response .= "Akarere: {$masjid['district']}\n";
                $response .= "Yakiriye: {$total} RWF\n";
                $response .= "Ingano: {$count}\n";
                $response .= "Uwa nyuma: {$last}";
            }
            
            echo $response;
            return;
        }

        // Fallback for invalid flows
        echo ($lang == 'en') 
            ? "END Invalid menu option." 
            : "END Ibyo wahisemo ntibikunze.";
            
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo ($lang == 'en') 
            ? "END System error occurred." 
            : "END Hari ikosa ry'ikoranabuhanga.";
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo ($lang == 'en') 
            ? "END Operation failed." 
            : "END Ntibyakunze.";
    }
}

function calculateUnpaidMonths($monthlyAmount, $totalPaid) {
    if ($monthlyAmount <= 0) return 0;
    return max(0, $monthlyAmount - $totalPaid);
}

function handleLanguageChange($level, $input) {
    global $lang;
    
    if ($level == 1) {
        echo "CON Choose Language\n1. English\n2. Kinyarwanda";
        return;
    } 
    
    if ($level == 2) {
        if (!isset($input[1])) {
            echo ($lang == 'en') ? "END Invalid selection." : "END Ibyo wakoze ntibyemewe.";
            return;
        }

        $newLang = match($input[1]) {
            '1' => 'en',
            '2' => 'kiny',
            default => null
        };

        if (!$newLang) {
            echo ($lang == 'en') ? "END Invalid choice." : "END Ibyo wahisemo ntibyemewe.";
            return;
        }

        // Update language in session
        $_SESSION['lang'] = $newLang;
        $lang = $newLang;

        // Reset all navigation state
        $_SESSION['ussd_state'] = [
            'current_menu' => '',
            'level' => 0,
            'data' => [],
            'lang' => $newLang
        ];
        
        // Return to main menu with new language and exit
        returnToMainMenu($newLang);
        return;
    }
}
function returnToMainMenu($lang) {
    // Reset navigation state when returning to main menu
    $_SESSION['ussd_state'] = [
        'current_menu' => '',
        'level' => 0,
        'lang' => $lang
    ];
    
    echo ($lang == 'en') ?
        "CON Welcome to the development of Islam\n1. Register\n2. Pay Contribution\n3. Check Account\n4. Hindura Ururimi" :
        "CON Murakaza neza mu iterambere rya Islam\n1. Kwiyandikisha\n2. Gutanga \n3. Kureba konte \n4. Change Language";
}

// Main USSD logic
if ($text == "") {
    // Initial request - initialize session and show main menu
    $_SESSION['ussd_state'] = [
        'current_menu' => '',
        'level' => 0,
        'lang' => $lang // Use the $lang variable that was set earlier
    ];
    returnToMainMenu($lang);
} else {
    $input = explode('*', $text);
    $userLevel = count($input);
    
    // Initialize state if not set
    if (!isset($_SESSION['ussd_state'])) {
        $_SESSION['ussd_state'] = [
            'current_menu' => $input[0] ?? '',
            'level' => $userLevel,
            'lang' => $lang
        ];
    }
    
    // Always use the session language
    $lang = $_SESSION['ussd_state']['lang'] ?? 'kiny';
    
    // Update current state
    $_SESSION['ussd_state']['current_menu'] = $input[0] ?? '';
    $_SESSION['ussd_state']['level'] = $userLevel;
    
    switch ($_SESSION['ussd_state']['current_menu']) {
        case '1':  
            handleRegistration($userLevel, $input, $phoneNumber, $pdo, $lang);
            break;
        case '2':  
            handleContribution($userLevel, $input, $phoneNumber, $pdo, $lang);
            break;
        case '3':  
                handleAccountCheck($userLevel, $input, $phoneNumber, $pdo, $lang);
            break;
        case '4':
            handleLanguageChange($userLevel, $input);
            break;
        default:  
            echo ($lang == 'en') ? "END Invalid choice." : "END Ibyo mwahisemo sibyo mwongere mugerageze.";
    }
}
