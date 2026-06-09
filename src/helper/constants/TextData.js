import DataStore from './DataStore'

let textValue = {
    HOME: DataStore.language == 'English' ?
        'HOME' : (
            DataStore.language == 'Hindi' ?
                'लैंडिंग पृष्ठ' : (
                    DataStore.language == 'Assamese' ?
                        'লেণ্ডিং পেজ' : (
                            DataStore.language == 'Bengali' ?
                                'অবতরণ পৃষ্ঠা' : 'HOME'
                        )
                )
        ),

    Delete: DataStore.language == 'English' ?
        'Delete' : (
            DataStore.language == 'Hindi' ?
                'मिटाना' : (
                    DataStore.language == 'Assamese' ?
                        'বিলোপ' : (
                            DataStore.language == 'Bengali' ?
                                'মুছে দিন' : 'Delete'
                        )
                )
        ),

    YES: DataStore.language == 'English' ?
        'YES' : (
            DataStore.language == 'Hindi' ?
                'हाँ' : (
                    DataStore.language == 'Assamese' ?
                        'হয়' : (
                            DataStore.language == 'Bengali' ?
                                'হ্যাঁ' : 'YES'
                        )
                )
        ),

    PROFILE: DataStore.language == 'English' ?
        'PROFILE' : (
            DataStore.language == 'Hindi' ?
                'प्रोफ़ाइल' : (
                    DataStore.language == 'Assamese' ?
                        'ৰূপৰেখা' : (
                            DataStore.language == 'Bengali' ?
                                'প্রোফাইল' : 'PROFILE'
                        )
                )
        ),

    ORDER_LIST: DataStore.language == 'English' ?
        'ORDER LIST' : (
            DataStore.language == 'Hindi' ?
                'आदेश सूची' : (
                    DataStore.language == 'Assamese' ?
                        'অৰ্ডাৰ তালিকা' : (
                            DataStore.language == 'Bengali' ?
                                'আদেশ তালিকা' : 'ORDER LIST'
                        )
                )
        ),

    ABOUT_US: DataStore.language == 'English' ?
        'ABOUT US' : (
            DataStore.language == 'Hindi' ?
                'हमारे बारे में' : (
                    DataStore.language == 'Assamese' ?
                        'আমাৰ বিষয়ে' : (
                            DataStore.language == 'Bengali' ?
                                'আমাদের সম্পর্কে' : 'ABOUT US'
                        )
                )
        ),

    CONTACT_US: DataStore.language == 'English' ?
        'CONTACT US' : (
            DataStore.language == 'Hindi' ?
                'हमसे संपर्क करें' : (
                    DataStore.language == 'Assamese' ?
                        'আমাৰ সৈতে যোগাযোগ কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'আমাদের সাথে যোগাযোগ করুন' : 'CONTACT US'
                        )
                )
        ),

    TERMS_CONDITIONS: DataStore.language == 'English' ?
        'TERMS & CONDITIONS' : (
            DataStore.language == 'Hindi' ?
                'नियम और शर्तें' : (
                    DataStore.language == 'Assamese' ?
                        'চৰ্ত আৰু নিয়ম' : (
                            DataStore.language == 'Bengali' ?
                                'নিয়ম ও শর্তাবলী' : 'TERMS & CONDITIONS'
                        )
                )
        ),

    PRIVACY_POLICY: DataStore.language == 'English' ?
        'PRIVACY POLICY' : (
            DataStore.language == 'Hindi' ?
                'गोपनीयता नीति' : (
                    DataStore.language == 'Assamese' ?
                        'গোপনীয়তা নীতি' : (
                            DataStore.language == 'Bengali' ?
                                'গোপনীয়তা নীতি' : 'PRIVACY POLICY'
                        )
                )
        ),

    Logout: DataStore.language == 'English' ?
        'Logout' : (
            DataStore.language == 'Hindi' ?
                'लॉग आउट' : (
                    DataStore.language == 'Assamese' ?
                        'লগআউট কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'লগআউট' : 'Logout'
                        )
                )
        ),

    Version: DataStore.language == 'English' ?
        'Version' : (
            DataStore.language == 'Hindi' ?
                'संस्करण' : (
                    DataStore.language == 'Assamese' ?
                        'সংস্কৰণ' : (
                            DataStore.language == 'Bengali' ?
                                'সংস্করণ' : 'Version'
                        )
                )
        ),

    Delete_Account: DataStore.language == 'English' ?
        'Delete Account' : (
            DataStore.language == 'Hindi' ?
                'खाता हटा दो' : (
                    DataStore.language == 'Assamese' ?
                        'একাউণ্ট মচি পেলাওক' : (
                            DataStore.language == 'Bengali' ?
                                'অ্যাকাউন্ট মুছুন' : 'Delete Account'
                        )
                )
        ),

    MASON_REGISTRATION: DataStore.language == 'English' ?
        'MASON REGISTRATION' : (
            DataStore.language == 'Hindi' ?
                'मेसन पंजीकरण' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন পঞ্জীয়ন' : (
                            DataStore.language == 'Bengali' ?
                                'ম্যাসন রেজিস্ট্রেশন' : 'MASON REGISTRATION'
                        )
                )
        ),

    LIFTING_HISTORY: DataStore.language == 'English' ?
        'LIFTING HISTORY' : (
            DataStore.language == 'Hindi' ?
                'उठाने का इतिहास' : (
                    DataStore.language == 'Assamese' ?
                        'ইতিহাস উত্তোলন কৰা' : (
                            DataStore.language == 'Bengali' ?
                                'উত্তোলন ইতিহাস' : 'LIFTING HISTORY'
                        )
                )
        ),

    REWARD_POINTS: DataStore.language == 'English' ?
        'REWARD POINTS' : (
            DataStore.language == 'Hindi' ?
                'ईनामी अंक' : (
                    DataStore.language == 'Assamese' ?
                        'পুৰস্কাৰ পইণ্ট' : (
                            DataStore.language == 'Bengali' ?
                                'পুরস্কার পয়েন্ট' : 'REWARD POINTS'
                        )
                )
        ),

    DASHBOARD: DataStore.language == 'English' ?
        'DASHBOARD' : (
            DataStore.language == 'Hindi' ?
                'डैशबोर्ड' : (
                    DataStore.language == 'Assamese' ?
                        "ডেচব'ৰ্ড" : (
                            DataStore.language == 'Bengali' ?
                                'ড্যাশবোর্ড' : 'DASHBOARD'
                        )
                )
        ),

    ADD_LIFTING: DataStore.language == 'English' ?
        'ADD LIFTING' : (
            DataStore.language == 'Hindi' ?
                'लिफ्टिंग जोड़ें' : (
                    DataStore.language == 'Assamese' ?
                        'লিফ্টিং যোগ কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'উত্তোলন যোগ করুন' : 'ADD LIFTING'
                        )
                )
        ),

    GIFT_CATALOGUE: DataStore.language == 'English' ?
        'GIFT CATALOGUE' : (
            DataStore.language == 'Hindi' ?
                'उपहार सूची' : (
                    DataStore.language == 'Assamese' ?
                        'উপহাৰৰ তালিকা' : (
                            DataStore.language == 'Bengali' ?
                                'উপহার ক্যাটালগ' : 'GIFT CATALOGUE'
                        )
                )
        ),

    REQUEST_A_DEALER: DataStore.language == 'English' ?
        'REQUEST A DEALER' : (
            DataStore.language == 'Hindi' ?
                'एक डीलर से अनुरोध करें' : (
                    DataStore.language == 'Assamese' ?
                        'এজন ডিলাৰক অনুৰোধ কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'একজন ডিলারকে অনুরোধ করুন' : 'REQUEST A DEALER'
                        )
                )
        ),

    MASON_SALES_ENTRY: DataStore.language == 'English' ?
        'MASON SALES ENTRY' : (
            DataStore.language == 'Hindi' ?
                'मेसन बिक्री प्रविष्टि' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন বিক্ৰী প্ৰৱেশ' : (
                            DataStore.language == 'Bengali' ?
                                'ম্যাসন সেলস এন্ট্রি' : 'MASON SALES ENTRY'
                        )
                )
        ),

    LIFTING_APPROVAL: DataStore.language == 'English' ?
        'LIFTING APPROVAL' : (
            DataStore.language == 'Hindi' ?
                'अनुमोदन उठाना' : (
                    DataStore.language == 'Assamese' ?
                        'উত্তোলনৰ অকাৰ্য্যকৰীতা' : (
                            DataStore.language == 'Bengali' ?
                                'উত্তোলন অনুমোদন' : 'LIFTING APPROVAL'
                        )
                )
        ),

    DEALER_LINK_REQUEST: DataStore.language == 'English' ?
        'DEALER LINK REQUEST' : (
            DataStore.language == 'Hindi' ?
                'डीलर लिंक अनुरोध' : (
                    DataStore.language == 'Assamese' ?
                        'ডিলাৰ লিংক অনুৰোধ' : (
                            DataStore.language == 'Bengali' ?
                                'ডিলার লিঙ্ক অনুরোধ' : 'DEALER LINK REQUEST'
                        )
                )
        ),

    Cancel: DataStore.language == 'English' ?
        'Cancel' : (
            DataStore.language == 'Hindi' ?
                'रद्द करना' : (
                    DataStore.language == 'Assamese' ?
                        'বাতিল কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'বাতিল করুন' : 'Cancel'
                        )
                )
        ),

    Edit_Profile: DataStore.language == 'English' ?
        'Edit Profile' : (
            DataStore.language == 'Hindi' ?
                'प्रोफ़ाइल संपादित करें' : (
                    DataStore.language == 'Assamese' ?
                        'প্ৰফাইল সম্পাদনা কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'প্রোফাইল সম্পাদনা করুন' : 'Edit Profile'
                        )
                )
        ),

    Save: DataStore.language == 'English' ?
        'Save' : (
            DataStore.language == 'Hindi' ?
                'बचाना' : (
                    DataStore.language == 'Assamese' ?
                        'সঞ্চয় কৰা' : (
                            DataStore.language == 'Bengali' ?
                                'সংরক্ষণ করুন' : 'Save'
                        )
                )
        ),

    Your_Name: DataStore.language == 'English' ?
        'Your Name' : (
            DataStore.language == 'Hindi' ?
                'आपका नाम' : (
                    DataStore.language == 'Assamese' ?
                        'তোমাৰ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'আপনার নাম' : 'Your Name'
                        )
                )
        ),

    Your_Phone_no: DataStore.language == 'English' ?
        'Your Phone no' : (
            DataStore.language == 'Hindi' ?
                'आपका फ़ोन नं' : (
                    DataStore.language == 'Assamese' ?
                        'আপোনাৰ ফোন নম্বৰ' : (
                            DataStore.language == 'Bengali' ?
                                'আপনার ফোন নম্বর' : 'Your Phone no'
                        )
                )
        ),

    Your_Mail: DataStore.language == 'English' ?
        'Your Mail' : (
            DataStore.language == 'Hindi' ?
                'आपकी मेल' : (
                    DataStore.language == 'Assamese' ?
                        'আপোনাৰ মেইল' : (
                            DataStore.language == 'Bengali' ?
                                'আপনার মেইল' : 'Your Mail'
                        )
                )
        ),

    Technical_Engineer_Name: DataStore.language == 'English' ?
        'Technical Engineer Name' : (
            DataStore.language == 'Hindi' ?
                'तकनीकी इंजीनियर का नाम' : (
                    DataStore.language == 'Assamese' ?
                        'কাৰিকৰী অভিযন্তাৰ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'টেকনিক্যাল ইঞ্জিনিয়ারের নাম' : 'Technical Engineer Name'
                        )
                )
        ),

    Technical_Engineer_Mobile: DataStore.language == 'English' ?
        'Technical Engineer Mobile' : (
            DataStore.language == 'Hindi' ?
                'तकनीकी इंजीनियर मोबाइल' : (
                    DataStore.language == 'Assamese' ?
                        'কাৰিকৰী অভিযন্তা মোবাইল' : (
                            DataStore.language == 'Bengali' ?
                                'টেকনিক্যাল ইঞ্জিনিয়ার মোবাইল' : 'Technical Engineer Mobile'
                        )
                )
        ),

    Aadhaar_no: DataStore.language == 'English' ?
        'Aadhaar no' : (
            DataStore.language == 'Hindi' ?
                'आधार नं' : (
                    DataStore.language == 'Assamese' ?
                        'আধাৰ নং' : (
                            DataStore.language == 'Bengali' ?
                                'আধার নং' : 'Aadhaar no'
                        )
                )
        ),

    Categoty: DataStore.language == 'English' ?
        'Categoty' : (
            DataStore.language == 'Hindi' ?
                'वर्ग' : (
                    DataStore.language == 'Assamese' ?
                        'শ্ৰেণী' : (
                            DataStore.language == 'Bengali' ?
                                'শ্রেণী' : 'Categoty'
                        )
                )
        ),

    Address_1: DataStore.language == 'English' ?
        'Address 1' : (
            DataStore.language == 'Hindi' ?
                'पता 1' : (
                    DataStore.language == 'Assamese' ?
                        'ঠিকনা ১' : (
                            DataStore.language == 'Bengali' ?
                                'ঠিকানা ১' : 'Address 1'
                        )
                )
        ),

    Address_2: DataStore.language == 'English' ?
        'Address 2' : (
            DataStore.language == 'Hindi' ?
                'पता 2' : (
                    DataStore.language == 'Assamese' ?
                        'ঠিকনা ২' : (
                            DataStore.language == 'Bengali' ?
                                'ঠিকানা ২' : 'Address 2'
                        )
                )
        ),

    City: DataStore.language == 'English' ?
        'City' : (
            DataStore.language == 'Hindi' ?
                'शहर' : (
                    DataStore.language == 'Assamese' ?
                        'চহৰ' : (
                            DataStore.language == 'Bengali' ?
                                'শহর' : 'City'
                        )
                )
        ),

    District: DataStore.language == 'English' ?
        'District' : (
            DataStore.language == 'Hindi' ?
                'ज़िला' : (
                    DataStore.language == 'Assamese' ?
                        'জিলা' : (
                            DataStore.language == 'Bengali' ?
                                'জেলা' : 'District'
                        )
                )
        ),

    State: DataStore.language == 'English' ?
        'State' : (
            DataStore.language == 'Hindi' ?
                'राज्य' : (
                    DataStore.language == 'Assamese' ?
                        'ৰাজ্য' : (
                            DataStore.language == 'Bengali' ?
                                'রাজ্য' : 'State'
                        )
                )
        ),

    Country: DataStore.language == 'English' ?
        'Country' : (
            DataStore.language == 'Hindi' ?
                'देश' : (
                    DataStore.language == 'Assamese' ?
                        'দেশ' : (
                            DataStore.language == 'Bengali' ?
                                'দেশ' : 'Country'
                        )
                )
        ),

    Pin: DataStore.language == 'English' ?
        'Pin' : (
            DataStore.language == 'Hindi' ?
                'ज़िप कोड' : (
                    DataStore.language == 'Assamese' ?
                        'জিপ কোড' : (
                            DataStore.language == 'Bengali' ?
                                'জিপ কোড' : 'Pin'
                        )
                )
        ),

    Employee_Id: DataStore.language == 'English' ?
        'Employee Id' : (
            DataStore.language == 'Hindi' ?
                'कर्मचारी आयडी' : (
                    DataStore.language == 'Assamese' ?
                        'কৰ্মচাৰীৰ আইডি' : (
                            DataStore.language == 'Bengali' ?
                                'কর্মচারী আইডি' : 'Employee Id'
                        )
                )
        ),

    Dealer_Code: DataStore.language == 'English' ?
        'Dealer Code' : (
            DataStore.language == 'Hindi' ?
                'डीलर कोड' : (
                    DataStore.language == 'Assamese' ?
                        "ডিলাৰ ক'ড" : (
                            DataStore.language == 'Bengali' ?
                                'ডিলার কোড' : 'Dealer Code'
                        )
                )
        ),

    Rssd_Code: DataStore.language == 'English' ?
        'Rssd Code' : (
            DataStore.language == 'Hindi' ?
                'आरएसएसडी कोड' : (
                    DataStore.language == 'Assamese' ?
                        'Rssd কোড' : (
                            DataStore.language == 'Bengali' ?
                                'আরএসএসডি কোড' : 'Rssd Code'
                        )
                )
        ),

    Enter_your_Mobile_number: DataStore.language == 'English' ?
        'Enter your\nMobile number' : (
            DataStore.language == 'Hindi' ?
                'अपना मोबाइल\nसंख्या दर्ज करे' : (
                    DataStore.language == 'Assamese' ?
                        'আপোনাৰ মোবাইল\nনম্বৰ দিয়ক' : (
                            DataStore.language == 'Bengali' ?
                                'আপনার মোবাইল\nনম্বর লিখুন' : 'Enter your\nMobile number'
                        )
                )
        ),

    Enter_mobile_number: DataStore.language == 'English' ?
        'Enter mobile number' : (
            DataStore.language == 'Hindi' ?
                'मोबाइल नंबर दर्ज करें' : (
                    DataStore.language == 'Assamese' ?
                        'মোবাইল নম্বৰ দিয়ক' : (
                            DataStore.language == 'Bengali' ?
                                'মোবাইল নম্বর লিখুন' : 'Enter mobile number'
                        )
                )
        ),

    Next: DataStore.language == 'English' ?
        'Next' : (
            DataStore.language == 'Hindi' ?
                'अगला' : (
                    DataStore.language == 'Assamese' ?
                        'পৰৱৰ্তী' : (
                            DataStore.language == 'Bengali' ?
                                'পরবর্তী' : 'Next'
                        )
                )
        ),

    Dont_have_an_account_yet: DataStore.language == 'English' ?
        "Don't have an account yet?" : (
            DataStore.language == 'Hindi' ?
                'अब तक कोई खाता नहीं है?' : (
                    DataStore.language == 'Assamese' ?
                        'এতিয়াও একাউণ্ট নাই নেকি?' : (
                            DataStore.language == 'Bengali' ?
                                'এখনও একটি অ্যাকাউন্ট নেই?' : "Don't have an account yet?"
                        )
                )
        ),

    for_Mason: DataStore.language == 'English' ?
        'for Mason' : (
            DataStore.language == 'Hindi' ?
                'मेसन के लिए' : (
                    DataStore.language == 'Assamese' ?
                        'মেছনৰ বাবে' : (
                            DataStore.language == 'Bengali' ?
                                'মেসনের জন্য' : 'for Mason'
                        )
                )
        ),

    Or: DataStore.language == 'English' ?
        'Or' : (
            DataStore.language == 'Hindi' ?
                'या' : (
                    DataStore.language == 'Assamese' ?
                        'অথবা' : (
                            DataStore.language == 'Bengali' ?
                                'বা' : 'Or'
                        )
                )
        ),

    for_Technical_Engineer: DataStore.language == 'English' ?
        'for Technical Engineer' : (
            DataStore.language == 'Hindi' ?
                'तकनीकी इंजीनियर के लिए' : (
                    DataStore.language == 'Assamese' ?
                        'কাৰিকৰী অভিযন্তাৰ বাবে' : (
                            DataStore.language == 'Bengali' ?
                                'টেকনিক্যাল ইঞ্জিনিয়ারের জন্য' : 'for Technical Engineer'
                        )
                )
        ),

    Verification_code: DataStore.language == 'English' ?
        'Verification code' : (
            DataStore.language == 'Hindi' ?
                'सत्यापन कोड' : (
                    DataStore.language == 'Assamese' ?
                        "সত্যাপন ক'ড" : (
                            DataStore.language == 'Bengali' ?
                                'যাচাইকরণ কোড' : 'Verification code'
                        )
                )
        ),

    Enter_code: DataStore.language == 'English' ?
        'Enter code' : (
            DataStore.language == 'Hindi' ?
                'कोड दर्ज करें' : (
                    DataStore.language == 'Assamese' ?
                        "ক'ড দিয়ক" : (
                            DataStore.language == 'Bengali' ?
                                'কোড লিখুন' : 'Enter code'
                        )
                )
        ),

    Please_enter_verification_code: DataStore.language == 'English' ?
        'Please enter verification code' : (
            DataStore.language == 'Hindi' ?
                'कृपया सत्यापन कोड दर्ज करें' : (
                    DataStore.language == 'Assamese' ?
                        "অনুগ্ৰহ কৰি সত্যাপন ক'ড দিয়ক" : (
                            DataStore.language == 'Bengali' ?
                                'যাচাইকরণ কোড লিখুন' : 'Please enter verification code'
                        )
                )
        ),

    Send_to_your_mobile_no: DataStore.language == 'English' ?
        'Send to your mobile no' : (
            DataStore.language == 'Hindi' ?
                'अपने मोबाइल नंबर पर भेजें' : (
                    DataStore.language == 'Assamese' ?
                        'আপোনাৰ মোবাইল নং ত পঠাওক' : (
                            DataStore.language == 'Bengali' ?
                                'আপনার মোবাইল নম্বরে পাঠান' : 'Send to your mobile no'
                        )
                )
        ),

    Didnt_receive_an_OTP: DataStore.language == 'English' ?
        "Didn't receive an OTP?" : (
            DataStore.language == 'Hindi' ?
                'ओटीपी नहीं मिला?' : (
                    DataStore.language == 'Assamese' ?
                        'অ’টিপি পোৱা নাছিল নেকি?' : (
                            DataStore.language == 'Bengali' ?
                                'একটি ওটিপি পাননি?' : "Didn't receive an OTP?"
                        )
                )
        ),

    Resend_OTP: DataStore.language == 'English' ?
        'Resend OTP' : (
            DataStore.language == 'Hindi' ?
                'ओटीपी दोबारा भेजें' : (
                    DataStore.language == 'Assamese' ?
                        'OTP পুনৰ প্ৰেৰণ কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'OTP আবার পাঠান' : 'Resend OTP'
                        )
                )
        ),

    Verify: DataStore.language == 'English' ?
        'Verify' : (
            DataStore.language == 'Hindi' ?
                'सत्यापित करें' : (
                    DataStore.language == 'Assamese' ?
                        'সত্যাপন কৰা' : (
                            DataStore.language == 'Bengali' ?
                                'যাচাই করুন' : 'Verify'
                        )
                )
        ),

    MASON: DataStore.language == 'English' ?
        'MASON' : (
            DataStore.language == 'Hindi' ?
                'राजमिस्त्री' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন' : (
                            DataStore.language == 'Bengali' ?
                                'ম্যাসন' : 'MASON'
                        )
                )
        ),

    REGISTRATION: DataStore.language == 'English' ?
        'REGISTRATION' : (
            DataStore.language == 'Hindi' ?
                'पंजीकरण' : (
                    DataStore.language == 'Assamese' ?
                        'পঞ্জীয়ন' : (
                            DataStore.language == 'Bengali' ?
                                'নিবন্ধন' : 'REGISTRATION'
                        )
                )
        ),

    Full_name_of_mason: DataStore.language == 'English' ?
        'Full name of mason' : (
            DataStore.language == 'Hindi' ?
                'पूरा नाम' : (
                    DataStore.language == 'Assamese' ?
                        'সম্পূৰ্ণ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'পুরো নাম' : 'Full name of mason'
                        )
                )
        ),

    Enter_Mobile_Number: DataStore.language == 'English' ?
        'Enter Mobile Number' : (
            DataStore.language == 'Hindi' ?
                'मोबाइल नंबर दर्ज करें' : (
                    DataStore.language == 'Assamese' ?
                        'মোবাইল নম্বৰ দিয়ক' : (
                            DataStore.language == 'Bengali' ?
                                'মোবাইল নম্বর লিখুন' : 'Enter Mobile Number'
                        )
                )
        ),

    Search: DataStore.language == 'English' ?
        'Search' : (
            DataStore.language == 'Hindi' ?
                'खोज' : (
                    DataStore.language == 'Assamese' ?
                        'সন্ধান' : (
                            DataStore.language == 'Bengali' ?
                                'অনুসন্ধান করুন' : 'Search'
                        )
                )
        ),

    Marital_Status: DataStore.language == 'English' ?
        'Marital Status' : (
            DataStore.language == 'Hindi' ?
                'वैवाहिक स्थिति' : (
                    DataStore.language == 'Assamese' ?
                        'বৈবাহিক অৱস্থা' : (
                            DataStore.language == 'Bengali' ?
                                'বৈবাহিক অবস্থা' : 'Marital Status'
                        )
                )
        ),

    Select_branch: DataStore.language == 'English' ?
        'Select branch' : (
            DataStore.language == 'Hindi' ?
                'शाखा का चयन करें' : (
                    DataStore.language == 'Assamese' ?
                        'শাখা নিৰ্বাচন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'শাখা নির্বাচন করুন' : 'Select branch'
                        )
                )
        ),

    Select_Technical_Engineer: DataStore.language == 'English' ?
        'Select Technical Engineer' : (
            DataStore.language == 'Hindi' ?
                'तकनीकी इंजीनियर का चयन करें' : (
                    DataStore.language == 'Assamese' ?
                        'কাৰিকৰী অভিযন্তা নিৰ্বাচন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'টেকনিক্যাল ইঞ্জিনিয়ার নির্বাচন করুন' : 'Select Technical Engineer'
                        )
                )
        ),

    DATE_OF_BIRTH: DataStore.language == 'English' ?
        'DATE OF BIRTH' : (
            DataStore.language == 'Hindi' ?
                'जन्म तिथि' : (
                    DataStore.language == 'Assamese' ?
                        'জন্ম তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'জন্ম তারিখ' : 'DATE OF BIRTH'
                        )
                )
        ),

    Enter_Aadhar_number: DataStore.language == 'English' ?
        'Enter Aadhar number' : (
            DataStore.language == 'Hindi' ?
                'आधार नंबर दर्ज करें' : (
                    DataStore.language == 'Assamese' ?
                        'আধাৰ নম্বৰ লিখক' : (
                            DataStore.language == 'Bengali' ?
                                'আধার নম্বর লিখুন' : 'Enter Aadhar number'
                        )
                )
        ),

    Choose_File: DataStore.language == 'English' ?
        'Choose File' : (
            DataStore.language == 'Hindi' ?
                'फाइलें चुनें' : (
                    DataStore.language == 'Assamese' ?
                        'ফাইল নিৰ্ব্বাচন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'ফাইল নির্বাচন করুন' : 'Choose File'
                        )
                )
        ),

    Upload_Aadhaar: DataStore.language == 'English' ?
        'Upload Aadhaar' : (
            DataStore.language == 'Hindi' ?
                'आधार अपलोड करें' : (
                    DataStore.language == 'Assamese' ?
                        'আধাৰ আপলোড কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'আধার আপলোড করুন' : 'Upload Aadhaar'
                        )
                )
        ),

    Register: DataStore.language == 'English' ?
        'Register' : (
            DataStore.language == 'Hindi' ?
                'पंजीकरण करवाना' : (
                    DataStore.language == 'Assamese' ?
                        'পঞ্জীয়ন কৰা' : (
                            DataStore.language == 'Bengali' ?
                                'নিবন্ধন করুন' : 'Register'
                        )
                )
        ),

    TECHNICAL_ENGINEER: DataStore.language == 'English' ?
        'TECHNICAL ENGINEER' : (
            DataStore.language == 'Hindi' ?
                'तकनीकी इंजीनियर' : (
                    DataStore.language == 'Assamese' ?
                        'কাৰিকৰী অভিযন্তা' : (
                            DataStore.language == 'Bengali' ?
                                'কারিগরি প্রকৌশলী' : 'TECHNICAL ENGINEER'
                        )
                )
        ),

    Full_name: DataStore.language == 'English' ?
        'Full name' : (
            DataStore.language == 'Hindi' ?
                'पूरा नाम' : (
                    DataStore.language == 'Assamese' ?
                        'সম্পূৰ্ণ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'পুরো নাম' : 'Full name'
                        )
                )
        ),

    Employee_Code: DataStore.language == 'English' ?
        'Employee Code' : (
            DataStore.language == 'Hindi' ?
                'कर्मचारी कोड' : (
                    DataStore.language == 'Assamese' ?
                        'কৰ্মচাৰীৰ সংহিতা' : (
                            DataStore.language == 'Bengali' ?
                                'কর্মচারী কোড' : 'Employee Code'
                        )
                )
        ),

    Phone: DataStore.language == 'English' ?
        'Phone' : (
            DataStore.language == 'Hindi' ?
                'फ़ोन' : (
                    DataStore.language == 'Assamese' ?
                        'ফোন' : (
                            DataStore.language == 'Bengali' ?
                                'ফোন' : 'Phone'
                        )
                )
        ),

    Your_number_is_verified: DataStore.language == 'English' ?
        'Your number is verified' : (
            DataStore.language == 'Hindi' ?
                'आपका नंबर सत्यापित है' : (
                    DataStore.language == 'Assamese' ?
                        'আপোনাৰ নম্বৰটো পৰীক্ষা কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'আপনার নম্বর যাচাই করা হয়েছে' : 'Your number is verified'
                        )
                )
        ),

        Successfully_language_changed: DataStore.language == 'English' ?
        'Successfully language changed' : (
            DataStore.language == 'Hindi' ?
                'सफलतापूर्वक भाषा बदल गई' : (
                    DataStore.language == 'Assamese' ?
                        'সফলতাৰে ভাষা সলনি হ’ল' : (
                            DataStore.language == 'Bengali' ?
                                'সফলভাবে ভাষা পরিবর্তন হয়েছে' : 'Your number is verified'
                        )
                )
        ),

    Login: DataStore.language == 'English' ?
        'Login' : (
            DataStore.language == 'Hindi' ?
                'लॉग इन करें' : (
                    DataStore.language == 'Assamese' ?
                        'লগইন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'লগইন করুন' : 'Login'
                        )
                )
        ),

    SUPPORT: DataStore.language == 'English' ?
        'SUPPORT' : (
            DataStore.language == 'Hindi' ?
                'सहायता' : (
                    DataStore.language == 'Assamese' ?
                        'সমৰ্থন' : (
                            DataStore.language == 'Bengali' ?
                                'সমর্থন' : 'SUPPORT'
                        )
                )
        ),

    Get_In_Touch: DataStore.language == 'English' ?
        'Get In Touch' : (
            DataStore.language == 'Hindi' ?
                'संपर्क में रहो' : (
                    DataStore.language == 'Assamese' ?
                        'স্পৰ্শ কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'যোগাযোগ করুন' : 'Get In Touch'
                        )
                )
        ),

    Message: DataStore.language == 'English' ?
        'Message' : (
            DataStore.language == 'Hindi' ?
                'संदेश' : (
                    DataStore.language == 'Assamese' ?
                        'বাৰ্তা' : (
                            DataStore.language == 'Bengali' ?
                                'বাৰ্তা' : 'Message'
                        )
                )
        ),

    Submit: DataStore.language == 'English' ?
        'Submit' : (
            DataStore.language == 'Hindi' ?
                'जमा करना' : (
                    DataStore.language == 'Assamese' ?
                        'দাখিল কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'জমা দিন' : 'Submit'
                        )
                )
        ),

    SUPPORT_LIST: DataStore.language == 'English' ?
        'SUPPORT LIST' : (
            DataStore.language == 'Hindi' ?
                'समर्थन सूची' : (
                    DataStore.language == 'Assamese' ?
                        'সমৰ্থন তালিকা' : (
                            DataStore.language == 'Bengali' ?
                                'সমর্থন তালিকা' : 'SUPPORT LIST'
                        )
                )
        ),

    Name: DataStore.language == 'English' ?
        'Name' : (
            DataStore.language == 'Hindi' ?
                'नाम' : (
                    DataStore.language == 'Assamese' ?
                        'নাম' : (
                            DataStore.language == 'Bengali' ?
                                'নাম' : 'Name'
                        )
                )
        ),

    Comment: DataStore.language == 'English' ?
        'Comment' : (
            DataStore.language == 'Hindi' ?
                'टिप्पणी' : (
                    DataStore.language == 'Assamese' ?
                        'মন্তব্য' : (
                            DataStore.language == 'Bengali' ?
                                'মন্তব্য' : 'Comment'
                        )
                )
        ),

    Status: DataStore.language == 'English' ?
        'Status' : (
            DataStore.language == 'Hindi' ?
                'स्थिति' : (
                    DataStore.language == 'Assamese' ?
                        'স্থিতি' : (
                            DataStore.language == 'Bengali' ?
                                'স্থিতি' : 'Status'
                        )
                )
        ),

    Pending: DataStore.language == 'English' ?
        'Pending' : (
            DataStore.language == 'Hindi' ?
                'लंबित' : (
                    DataStore.language == 'Assamese' ?
                        'বাকী থকা' : (
                            DataStore.language == 'Bengali' ?
                                'মুলতুবি' : 'Pending'
                        )
                )
        ),

    Resolved: DataStore.language == 'English' ?
        'Resolved' : (
            DataStore.language == 'Hindi' ?
                'हल किया' : (
                    DataStore.language == 'Assamese' ?
                        'সমাধান কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'সমাধান করা হয়েছে' : 'Resolved'
                        )
                )
        ),

    Rejected: DataStore.language == 'English' ?
        'Rejected' : (
            DataStore.language == 'Hindi' ?
                'अस्वीकार कर दिया' : (
                    DataStore.language == 'Assamese' ?
                        'নাকচ কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'প্রত্যাখ্যাত' : 'Rejected'
                        )
                )
        ),

    No_Data_Found: DataStore.language == 'English' ?
        'No Data Found' : (
            DataStore.language == 'Hindi' ?
                'डाटा प्राप्त नहीं हुआ' : (
                    DataStore.language == 'Assamese' ?
                        "কোনো তথ্য পোৱা নগ'ল" : (
                            DataStore.language == 'Bengali' ?
                                'কোন তথ্য পাওয়া যায়নি' : 'No Data Found'
                        )
                )
        ),

    REJECT: DataStore.language == 'English' ?
        'REJECT' : (
            DataStore.language == 'Hindi' ?
                'अस्वीकार करना' : (
                    DataStore.language == 'Assamese' ?
                        'প্ৰত্যাখ্যান' : (
                            DataStore.language == 'Bengali' ?
                                'প্ৰত্যাখ্যান' : 'REJECT'
                        )
                )
        ),

    ACCEPT: DataStore.language == 'English' ?
        'ACCEPT' : (
            DataStore.language == 'Hindi' ?
                'स्वीकार करना' : (
                    DataStore.language == 'Assamese' ?
                        'গ্ৰহণ কৰা' : (
                            DataStore.language == 'Bengali' ?
                                'স্বীকার করুন' : 'ACCEPT'
                        )
                )
        ),

    Approved: DataStore.language == 'English' ?
        'Approved' : (
            DataStore.language == 'Hindi' ?
                'अनुमत' : (
                    DataStore.language == 'Assamese' ?
                        'অনুমোদিত' : (
                            DataStore.language == 'Bengali' ?
                                'অনুমোদিত' : 'Approved'
                        )
                )
        ),

    Are_you_want_to_accept_the_lifting: DataStore.language == 'English' ?
        'Are you want to\naccept the lifting?' : (
            DataStore.language == 'Hindi' ?
                'क्या आप लिफ्ट स्वीकार\nकरना चाहते हैं?' : (
                    DataStore.language == 'Assamese' ?
                        'লিফ্টখন গ্ৰহণ কৰিব\nবিচাৰে নেকি?' : (
                            DataStore.language == 'Bengali' ?
                                'আপনি কি লিফট\nগ্রহণ করতে চান?' : 'Are you want to\naccept the lifting?'
                        )
                )
        ),

    Select_your_reason: DataStore.language == 'English' ?
        'Select your reason' : (
            DataStore.language == 'Hindi' ?
                'अपना कारण चुनें' : (
                    DataStore.language == 'Assamese' ?
                        'আপোনাৰ কাৰণ বাছক' : (
                            DataStore.language == 'Bengali' ?
                                'আপনার কারণ নির্বাচন করুন' : 'Select your reason'
                        )
                )
        ),

    DATE: DataStore.language == 'English' ?
        'DATE' : (
            DataStore.language == 'Hindi' ?
                'तारीख' : (
                    DataStore.language == 'Assamese' ?
                        'তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'তারিখ' : 'DATE'
                        )
                )
        ),

    DESCRIPTION: DataStore.language == 'English' ?
        'DESCRIPTION' : (
            DataStore.language == 'Hindi' ?
                'विवरण' : (
                    DataStore.language == 'Assamese' ?
                        'বিৱৰণ' : (
                            DataStore.language == 'Bengali' ?
                                'বর্ণনা' : 'DESCRIPTION'
                        )
                )
        ),

    POINTS: DataStore.language == 'English' ?
        'POINTS' : (
            DataStore.language == 'Hindi' ?
                'अंक' : (
                    DataStore.language == 'Assamese' ?
                        'পয়েন্ট' : (
                            DataStore.language == 'Bengali' ?
                                'পয়েন্ট' : 'POINTS'
                        )
                )
        ),

    EARNED: DataStore.language == 'English' ?
        'EARNED' : (
            DataStore.language == 'Hindi' ?
                'अर्जित' : (
                    DataStore.language == 'Assamese' ?
                        'অর্জিত' : (
                            DataStore.language == 'Bengali' ?
                                'অর্জিত' : 'EARNED'
                        )
                )
        ),

    REDEEMED: DataStore.language == 'English' ?
        'REDEEMED' : (
            DataStore.language == 'Hindi' ?
                'छुड़ाया' : (
                    DataStore.language == 'Assamese' ?
                        'মুক্ত কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'খালাস' : 'REDEEMED'
                        )
                )
        ),

    REWARD_DETAILS: DataStore.language == 'English' ?
        'REWARD DETAILS' : (
            DataStore.language == 'Hindi' ?
                'पुरस्कार विवरण' : (
                    DataStore.language == 'Assamese' ?
                        'পুৰস্কাৰৰ বিৱৰণ' : (
                            DataStore.language == 'Bengali' ?
                                'পুরস্কার বিবরণ' : 'REWARD DETAILS'
                        )
                )
        ),

    NET_POINTS: DataStore.language == 'English' ?
        'NET POINTS' : (
            DataStore.language == 'Hindi' ?
                'शुद्ध अंक' : (
                    DataStore.language == 'Assamese' ?
                        'নেট পইণ্ট' : (
                            DataStore.language == 'Bengali' ?
                                'নেট পয়েন্ট' : 'NET POINTS'
                        )
                )
        ),

    Month: DataStore.language == 'English' ?
        'Month' : (
            DataStore.language == 'Hindi' ?
                'महीना' : (
                    DataStore.language == 'Assamese' ?
                        'মাহ' : (
                            DataStore.language == 'Bengali' ?
                                'মাস' : 'Month'
                        )
                )
        ),

    Year: DataStore.language == 'English' ?
        'Year' : (
            DataStore.language == 'Hindi' ?
                'वर्ष' : (
                    DataStore.language == 'Assamese' ?
                        'বছৰ' : (
                            DataStore.language == 'Bengali' ?
                                'বছর' : 'Year'
                        )
                )
        ),

    Select_Mason: DataStore.language == 'English' ?
        'Select Mason' : (
            DataStore.language == 'Hindi' ?
                'मेसन का चयन करें' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন নিৰ্বাচন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'ম্যাসন নির্বাচন করুন' : 'Select Mason'
                        )
                )
        ),

    Redeem_Now: DataStore.language == 'English' ?
        'Redeem Now' : (
            DataStore.language == 'Hindi' ?
                'अब एवज करें' : (
                    DataStore.language == 'Assamese' ?
                        'এতিয়াই ৰিডিম কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'এখনই রিডিম করুন' : 'Redeem Now'
                        )
                )
        ),

    LINK: DataStore.language == 'English' ?
        'LINK' : (
            DataStore.language == 'Hindi' ?
                'जोड़ना' : (
                    DataStore.language == 'Assamese' ?
                        'লিংক' : (
                            DataStore.language == 'Bengali' ?
                                'লিঙ্ক' : 'LINK'
                        )
                )
        ),

    Registration: DataStore.language == 'English' ?
        'Registration' : (
            DataStore.language == 'Hindi' ?
                'पंजीकरण' : (
                    DataStore.language == 'Assamese' ?
                        'পঞ্জীয়ন' : (
                            DataStore.language == 'Bengali' ?
                                'নিবন্ধন' : 'Registration'
                        )
                )
        ),

    Successful: DataStore.language == 'English' ?
        'Successful' : (
            DataStore.language == 'Hindi' ?
                'सफल' : (
                    DataStore.language == 'Assamese' ?
                        'সফল' : (
                            DataStore.language == 'Bengali' ?
                                'সফল' : 'Successful'
                        )
                )
        ),

    Order_Id: DataStore.language == 'English' ?
        'Order Id' : (
            DataStore.language == 'Hindi' ?
                'आदेश कामतत्व' : (
                    DataStore.language == 'Assamese' ?
                        'অৰ্ডাৰ আইডি' : (
                            DataStore.language == 'Bengali' ?
                                'অর্ডার আইডি' : 'Order Id'
                        )
                )
        ),

    Date: DataStore.language == 'English' ?
        'Date' : (
            DataStore.language == 'Hindi' ?
                'तारीख' : (
                    DataStore.language == 'Assamese' ?
                        'তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'তারিখ' : 'Date'
                        )
                )
        ),

    Description: DataStore.language == 'English' ?
        'Description' : (
            DataStore.language == 'Hindi' ?
                'विवरण' : (
                    DataStore.language == 'Assamese' ?
                        'বিৱৰণ' : (
                            DataStore.language == 'Bengali' ?
                                'বর্ণনা' : 'Description'
                        )
                )
        ),

    Points: DataStore.language == 'English' ?
        'Points' : (
            DataStore.language == 'Hindi' ?
                'अंक' : (
                    DataStore.language == 'Assamese' ?
                        'পইণ্ট' : (
                            DataStore.language == 'Bengali' ?
                                'পয়েন্ট' : 'Points'
                        )
                )
        ),

    Delivery_Status: DataStore.language == 'English' ?
        'Delivery Status' : (
            DataStore.language == 'Hindi' ?
                'डिलीवरी स्टेटस' : (
                    DataStore.language == 'Assamese' ?
                        'ডেলিভাৰীৰ অৱস্থা' : (
                            DataStore.language == 'Bengali' ?
                                'ডেলিভারি স্ট্যাটাস' : 'Delivery Status'
                        )
                )
        ),

    Delivered: DataStore.language == 'English' ?
        'Delivered' : (
            DataStore.language == 'Hindi' ?
                'पहुंचा दिया' : (
                    DataStore.language == 'Assamese' ?
                        'বিলি কৰা হ’ল' : (
                            DataStore.language == 'Bengali' ?
                                'বিতরণ করা হয়েছে' : 'Delivered'
                        )
                )
        ),

    Delivery_Date: DataStore.language == 'English' ?
        'Delivery Date' : (
            DataStore.language == 'Hindi' ?
                'डिलीवरी की तारीख' : (
                    DataStore.language == 'Assamese' ?
                        'ডেলিভাৰীৰ তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'ডেলিভারি তারিখ' : 'Delivery Date'
                        )
                )
        ),

    Support: DataStore.language == 'English' ?
        'Support' : (
            DataStore.language == 'Hindi' ?
                'सहायता' : (
                    DataStore.language == 'Assamese' ?
                        'সমৰ্থন' : (
                            DataStore.language == 'Bengali' ?
                                'সমর্থন' : 'Support'
                        )
                )
        ),

    View_Support: DataStore.language == 'English' ?
        'View Support' : (
            DataStore.language == 'Hindi' ?
                'समर्थन देखें' : (
                    DataStore.language == 'Assamese' ?
                        'সমৰ্থন চাওক' : (
                            DataStore.language == 'Bengali' ?
                                'সমর্থন দেখুন' : 'View Support'
                        )
                )
        ),

    NOTIFICATION: DataStore.language == 'English' ?
        'NOTIFICATION' : (
            DataStore.language == 'Hindi' ?
                'अधिसूचना' : (
                    DataStore.language == 'Assamese' ?
                        'জাননী' : (
                            DataStore.language == 'Bengali' ?
                                'বিজ্ঞপ্তি' : 'NOTIFICATION'
                        )
                )
        ),

    Enter_Code: DataStore.language == 'English' ?
        'Enter Code' : (
            DataStore.language == 'Hindi' ?
                'कोड दर्ज करें' : (
                    DataStore.language == 'Assamese' ?
                        "ক'ড দিয়ক" : (
                            DataStore.language == 'Bengali' ?
                                'কোড লিখুন' : 'Enter Code'
                        )
                )
        ),

    Please_Enter_Verification_Code: DataStore.language == 'English' ?
        'Please Enter Verification Code' : (
            DataStore.language == 'Hindi' ?
                'कृपया सत्यापन कोड दर्ज करें' : (
                    DataStore.language == 'Assamese' ?
                        "অনুগ্ৰহ কৰি ভেৰিফিকেচন ক'ড দিয়ক" : (
                            DataStore.language == 'Bengali' ?
                                'অনুগ্রহ করে যাচাইকরণ কোড লিখুন' : 'Please Enter Verification Code'
                        )
                )
        ),

    Send_To_Your_Mobile: DataStore.language == 'English' ?
        'Send To Your Mobile' : (
            DataStore.language == 'Hindi' ?
                'अपने मोबाइल पर भेजें' : (
                    DataStore.language == 'Assamese' ?
                        'আপোনাৰ মোবাইললৈ পঠাওক' : (
                            DataStore.language == 'Bengali' ?
                                'আপনার মোবাইলে পাঠান' : 'Send To Your Mobile'
                        )
                )
        ),

    Spouse_name: DataStore.language == 'English' ?
        'Spouse name' : (
            DataStore.language == 'Hindi' ?
                'जीवनसाथी का नाम' : (
                    DataStore.language == 'Assamese' ?
                        'পত্নীৰ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'পত্নীর নাম' : 'Spouse name'
                        )
                )
        ),

    SPOUSES_DATE_OF_BIRTH: DataStore.language == 'English' ?
        "SPOUSE'S DATE OF BIRTH" : (
            DataStore.language == 'Hindi' ?
                'पति के जन्म की तारीख' : (
                    DataStore.language == 'Assamese' ?
                        'পত্নীৰ জন্ম তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'স্ত্রীর জন্ম তারিখ' : "SPOUSE'S DATE OF BIRTH"
                        )
                )
        ),

    EDIT: DataStore.language == 'English' ?
        'EDIT' : (
            DataStore.language == 'Hindi' ?
                'संपादन करना' : (
                    DataStore.language == 'Assamese' ?
                        'সম্পাদনা কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'সম্পাদনা করুন' : 'EDIT'
                        )
                )
        ),

    From_Date: DataStore.language == 'English' ?
        'From Date' : (
            DataStore.language == 'Hindi' ?
                'की तिथि से' : (
                    DataStore.language == 'Assamese' ?
                        'তাৰিখৰ পৰা' : (
                            DataStore.language == 'Bengali' ?
                                'তারিখ থেকে' : 'From Date'
                        )
                )
        ),

    To_Date: DataStore.language == 'English' ?
        'To Date' : (
            DataStore.language == 'Hindi' ?
                'तारीख तक' : (
                    DataStore.language == 'Assamese' ?
                        'আজিলৈকে' : (
                            DataStore.language == 'Bengali' ?
                                'তারিখ থেকে' : 'To Date'
                        )
                )
        ),

    Are_you_want_to_accept_the_lifting: DataStore.language == 'English' ?
        'Are you want to\naccept the lifting?' : (
            DataStore.language == 'Hindi' ?
                'क्या आप उठाव\nस्वीकार करना चाहते हैं?' : (
                    DataStore.language == 'Assamese' ?
                        'লিফটিংটো গ্ৰহণ\nকৰিব বিচাৰিছে নেকি?' : (
                            DataStore.language == 'Bengali' ?
                                'আপনি উত্তোলন\nগ্রহণ করতে চান?' : 'Are you want to\naccept the lifting?'
                        )
                )
        ),

    Edit_Pending_Lifting: DataStore.language == 'English' ?
        'Edit Pending Lifting' : (
            DataStore.language == 'Hindi' ?
                'लंबित उठान संपादित करें' : (
                    DataStore.language == 'Assamese' ?
                        'সম্পাদনা বাকী থকা উত্তোলন' : (
                            DataStore.language == 'Bengali' ?
                                'মুলতুবি উত্তোলন সম্পাদনা করুন' : 'Edit Pending Lifting'
                        )
                )
        ),

    Product: DataStore.language == 'English' ?
        'Product' : (
            DataStore.language == 'Hindi' ?
                'उत्पाद' : (
                    DataStore.language == 'Assamese' ?
                        'সামগ্ৰী' : (
                            DataStore.language == 'Bengali' ?
                                'সামগ্ৰী' : 'Product'
                        )
                )
        ),

    Lifting_Date: DataStore.language == 'English' ?
        'Lifting Date' : (
            DataStore.language == 'Hindi' ?
                'उठाने की तिथि' : (
                    DataStore.language == 'Assamese' ?
                        'উত্তোলনৰ তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'উত্তোলনের তারিখ' : 'Lifting Date'
                        )
                )
        ),

    Mason_Name: DataStore.language == 'English' ?
        'Mason Name' : (
            DataStore.language == 'Hindi' ?
                'मेसन नाम' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন নাম' : (
                            DataStore.language == 'Bengali' ?
                                'মেসন নাম' : 'Mason Name'
                        )
                )
        ),

    Mason_Phone: DataStore.language == 'English' ?
        'Mason Phone' : (
            DataStore.language == 'Hindi' ?
                'मेसन फ़ोन' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন ফোন' : (
                            DataStore.language == 'Bengali' ?
                                'মেসন ফোন' : 'Mason Phone'
                        )
                )
        ),

    No_of_Bags: DataStore.language == 'English' ?
        'No of Bags' : (
            DataStore.language == 'Hindi' ?
                'बैग की संख्या' : (
                    DataStore.language == 'Assamese' ?
                        'বেগসমূহৰ সংখ্যা' : (
                            DataStore.language == 'Bengali' ?
                                'ব্যাগের সংখ্যা' : 'No of Bags'
                        )
                )
        ),

    Accept: DataStore.language == 'English' ?
        'Accept' : (
            DataStore.language == 'Hindi' ?
                'स्वीकार करना' : (
                    DataStore.language == 'Assamese' ?
                        'গ্ৰহণ কৰা' : (
                            DataStore.language == 'Bengali' ?
                                'গ্রহণ করুন' : 'Accept'
                        )
                )
        ),

    PRODUCTS: DataStore.language == 'English' ?
        'PRODUCTS' : (
            DataStore.language == 'Hindi' ?
                'उत्पादों' : (
                    DataStore.language == 'Assamese' ?
                        'সামগ্ৰী' : (
                            DataStore.language == 'Bengali' ?
                                'পণ্য' : 'PRODUCTS'
                        )
                )
        ),

    NO_OF_BAGS: DataStore.language == 'English' ?
        'NO OF BAGS' : (
            DataStore.language == 'Hindi' ?
                'बैग की संख्या' : (
                    DataStore.language == 'Assamese' ?
                        'বেগসমূহৰ সংখ্যা' : (
                            DataStore.language == 'Bengali' ?
                                'ব্যাগের সংখ্যা' : 'NO OF BAGS'
                        )
                )
        ),

    LIFTING_DATE: DataStore.language == 'English' ?
        'LIFTING DATE' : (
            DataStore.language == 'Hindi' ?
                'उठाने की तिथि' : (
                    DataStore.language == 'Assamese' ?
                        'উত্তোলনৰ তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'উত্তোলনের তারিখ' : 'LIFTING DATE'
                        )
                )
        ),

    POINTS: DataStore.language == 'English' ?
        'POINTS' : (
            DataStore.language == 'Hindi' ?
                'अंक' : (
                    DataStore.language == 'Assamese' ?
                        'পইণ্ট' : (
                            DataStore.language == 'Bengali' ?
                                'পয়েন্ট' : 'POINTS'
                        )
                )
        ),

    In_progress: DataStore.language == 'English' ?
        'In-progress' : (
            DataStore.language == 'Hindi' ?
                'प्रगति पर है' : (
                    DataStore.language == 'Assamese' ?
                        'প্ৰগতিশীল' : (
                            DataStore.language == 'Bengali' ?
                                'চলছে' : 'In-progress'
                        )
                )
        ),

    DEALER: DataStore.language == 'English' ?
        'DEALER' : (
            DataStore.language == 'Hindi' ?
                'डीलर' : (
                    DataStore.language == 'Assamese' ?
                        'ডিলাৰ' : (
                            DataStore.language == 'Bengali' ?
                                'ডিলার' : 'DEALER'
                        )
                )
        ),

    RSSD: DataStore.language == 'English' ?
        'RSSD' : (
            DataStore.language == 'Hindi' ?
                'आरएसएसडी' : (
                    DataStore.language == 'Assamese' ?
                        'ৰছীৰে বন্ধা' : (
                            DataStore.language == 'Bengali' ?
                                'আরএসএসডি' : 'RSSD'
                        )
                )
        ),

    SUB_DEALER: DataStore.language == 'English' ?
        'SUB-DEALER' : (
            DataStore.language == 'Hindi' ?
                'उप-डीलर' : (
                    DataStore.language == 'Assamese' ?
                        'সাব-ডিলাৰ' : (
                            DataStore.language == 'Bengali' ?
                                'সাব-ডিলার' : 'SUB-DEALER'
                        )
                )
        ),

    STAR_SAATHI_STATUS: DataStore.language == 'English' ?
        'STAR SAATHI STATUS' : (
            DataStore.language == 'Hindi' ?
                'स्टार साथी स्थिति' : (
                    DataStore.language == 'Assamese' ?
                        'তাৰকা অংশীদাৰৰ অৱস্থা' : (
                            DataStore.language == 'Bengali' ?
                                'তারকা অংশীদার অবস্থা' : 'STAR SAATHI STATUS'
                        )
                )
        ),

    APPROVED_REJECTED_DATE_AND_TIME: DataStore.language == 'English' ?
        'APPROVED/REJECTED DATE AND TIME' : (
            DataStore.language == 'Hindi' ?
                'स्वीकृत/अस्वीकृत दिनांक और समय' : (
                    DataStore.language == 'Assamese' ?
                        'অনুমোদিত/প্ৰত্যাখ্যান কৰা তাৰিখ আৰু সময়' : (
                            DataStore.language == 'Bengali' ?
                                'অনুমোদিত/প্রত্যাখ্যাত তারিখ এবং সময়' : 'APPROVED/REJECTED DATE AND TIME'
                        )
                )
        ),

    Search_Name: DataStore.language == 'English' ?
        'Search Name' : (
            DataStore.language == 'Hindi' ?
                'नाम खोजें' : (
                    DataStore.language == 'Assamese' ?
                        'সন্ধানৰ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'অনুসন্ধান নাম' : 'Search Name'
                        )
                )
        ),

    Mobile_No: DataStore.language == 'English' ?
        'Mobile No' : (
            DataStore.language == 'Hindi' ?
                'मोबाइल नहीं है' : (
                    DataStore.language == 'Assamese' ?
                        'মোবাইল নম্বৰ' : (
                            DataStore.language == 'Bengali' ?
                                'মোবাইল নম্বর' : 'Mobile No'
                        )
                )
        ),

    Aadhar: DataStore.language == 'English' ?
        'Aadhar' : (
            DataStore.language == 'Hindi' ?
                'आधार' : (
                    DataStore.language == 'Assamese' ?
                        'আধাৰ' : (
                            DataStore.language == 'Bengali' ?
                                'আধার' : 'Aadhar'
                        )
                )
        ),

    UPDATE_LIFTING_DETAILS: DataStore.language == 'English' ?
        'UPDATE LIFTING DETAILS' : (
            DataStore.language == 'Hindi' ?
                'उठाने का विवरण अद्यतन करें' : (
                    DataStore.language == 'Assamese' ?
                        'উত্তোলনৰ বিৱৰণ আপডেট কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'উত্তোলনের বিবরণ আপডেট করুন' : 'UPDATE LIFTING DETAILS'
                        )
                )
        ),

    Select_Product: DataStore.language == 'English' ?
        'Select Product' : (
            DataStore.language == 'Hindi' ?
                'उत्पाद का चयन करें' : (
                    DataStore.language == 'Assamese' ?
                        'পণ্য নিৰ্ব্বাচন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'পণ্য নির্বাচন করুন' : 'Select Product'
                        )
                )
        ),

    Enter_Qty_No_Of_Bags: DataStore.language == 'English' ?
        'Enter Qty(No Of Bags)' : (
            DataStore.language == 'Hindi' ?
                'मात्रा दर्ज करें (बैग की संख्या)' : (
                    DataStore.language == 'Assamese' ?
                        'পৰিমাণ (বেগৰ সংখ্যা) লিখক' : (
                            DataStore.language == 'Bengali' ?
                                'পরিমাণ লিখুন (ব্যাগের সংখ্যা)' : 'Enter Qty(No Of Bags)'
                        )
                )
        ),

    Remarks: DataStore.language == 'English' ?
        'Remarks' : (
            DataStore.language == 'Hindi' ?
                'टिप्पणी' : (
                    DataStore.language == 'Assamese' ?
                        'মন্তব্য' : (
                            DataStore.language == 'Bengali' ?
                                'মন্তব্য' : 'Remarks'
                        )
                )
        ),

    CANCEL: DataStore.language == 'English' ?
        'CANCEL' : (
            DataStore.language == 'Hindi' ?
                'रद्द करना' : (
                    DataStore.language == 'Assamese' ?
                        'বাতিল কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'বাতিল করুন' : 'CANCEL'
                        )
                )
        ),

    UPDATE: DataStore.language == 'English' ?
        'UPDATE' : (
            DataStore.language == 'Hindi' ?
                'अद्यतन' : (
                    DataStore.language == 'Assamese' ?
                        'নবীকৰণ' : (
                            DataStore.language == 'Bengali' ?
                                'আপডেট করুন' : 'UPDATE'
                        )
                )
        ),

    GIFT_REDEMPTION: DataStore.language == 'English' ?
        'GIFT REDEMPTION' : (
            DataStore.language == 'Hindi' ?
                'उपहार मोचन' : (
                    DataStore.language == 'Assamese' ?
                        'উপহাৰ মুক্তি' : (
                            DataStore.language == 'Bengali' ?
                                'উপহার রিডেম্পশন' : 'GIFT REDEMPTION'
                        )
                )
        ),

    Address: DataStore.language == 'English' ?
        'Address' : (
            DataStore.language == 'Hindi' ?
                'पता' : (
                    DataStore.language == 'Assamese' ?
                        'ঠিকনা' : (
                            DataStore.language == 'Bengali' ?
                                'ঠিকানা' : 'Address'
                        )
                )
        ),

    t1: DataStore.language == 'English' ?
        'The iOS input suggestion requires React Native 0.58+ and works for iOS 12 and above. The iOS input suggestion requires React Native 0.58+ and works for iOS 12 and above.' : (
            DataStore.language == 'Hindi' ?
                'iOS इनपुट सुझाव के लिए रिएक्ट नेटिव 0.58+ की आवश्यकता होती है और यह iOS 12 और इसके बाद के संस्करण के लिए काम करता है। iOS इनपुट सुझाव के लिए रिएक्ट नेटिव 0.58+ की आवश्यकता होती है और यह iOS 12 और इसके बाद के संस्करण के लिए काम करता है।' : (
                    DataStore.language == 'Assamese' ?
                        'iOS ইনপুট পৰামৰ্শৰ বাবে React Native 0.58+ ৰ প্ৰয়োজন আৰু ই iOS 12 আৰু তাৰ ওপৰৰ বাবে কাম কৰে। iOS ইনপুট পৰামৰ্শৰ বাবে React Native 0.58+ ৰ প্ৰয়োজন আৰু ই iOS 12 আৰু তাৰ ওপৰৰ বাবে কাম কৰে।' : (
                            DataStore.language == 'Bengali' ?
                                'iOS ইনপুট সাজেশনের জন্য রিঅ্যাক্ট নেটিভ 0.58+ প্রয়োজন এবং iOS 12 এবং তার উপরে কাজ করে। iOS ইনপুট সাজেশনের জন্য রিঅ্যাক্ট নেটিভ 0.58+ প্রয়োজন এবং iOS 12 এবং তার উপরে কাজ করে।' : 'The iOS input suggestion requires React Native 0.58+ and works for iOS 12 and above. The iOS input suggestion requires React Native 0.58+ and works for iOS 12 and above.'
                        )
                )
        ),

    Total_Bags: DataStore.language == 'English' ?
        'Total Bags' : (
            DataStore.language == 'Hindi' ?
                'कुल बैग' : (
                    DataStore.language == 'Assamese' ?
                        'মুঠ বেগ' : (
                            DataStore.language == 'Bengali' ?
                                'মোট ব্যাগ' : 'Total Bags'
                        )
                )
        ),

    Are_you_sure_to_redeemed_this_product: DataStore.language == 'English' ?
        'Are you sure to redeemed this product?' : (
            DataStore.language == 'Hindi' ?
                'क्या आप निश्चित रूप से इस उत्पाद को भुना लेंगे?' : (
                    DataStore.language == 'Assamese' ?
                        'এই প্ৰডাক্টটো আপুনি নিশ্চিতভাৱে ৰিডিম কৰিছেনে?' : (
                            DataStore.language == 'Bengali' ?
                                'আপনি কি এই পণ্যটি রিডিম করার বিষয়ে নিশ্চিত?' : 'Are you sure to redeemed this product?'
                        )
                )
        ),

    Redeem: DataStore.language == 'English' ?
        'Redeem' : (
            DataStore.language == 'Hindi' ?
                'भुनाना' : (
                    DataStore.language == 'Assamese' ?
                        'ৰিডীম কৰা' : (
                            DataStore.language == 'Bengali' ?
                                'খালাস' : 'Redeem'
                        )
                )
        ),

    FAQ: DataStore.language == 'English' ?
        'FAQ' : (
            DataStore.language == 'Hindi' ?
                'अक्सर पूछे जाने वाले प्रश्नों' : (
                    DataStore.language == 'Assamese' ?
                        'সঘনাই সোধা প্ৰশ্ন' : (
                            DataStore.language == 'Bengali' ?
                                'প্রায়শই জিজ্ঞাসিত প্রশ্নাবলী' : 'FAQ'
                        )
                )
        ),

    Total_Linked_Mason: DataStore.language == 'English' ?
        'Total Linked Mason' : (
            DataStore.language == 'Hindi' ?
                'कुल लिंक्ड मेसन' : (
                    DataStore.language == 'Assamese' ?
                        'মুঠ সংযুক্ত মেছন' : (
                            DataStore.language == 'Bengali' ?
                                'মোট লিঙ্কড মেসন' : 'Total Linked Mason'
                        )
                )
        ),

    Active_Mason: DataStore.language == 'English' ?
        'Active Mason' : (
            DataStore.language == 'Hindi' ?
                'सक्रिय मेसन' : (
                    DataStore.language == 'Assamese' ?
                        'সক্ৰিয় মেছন' : (
                            DataStore.language == 'Bengali' ?
                                'সক্রিয় মেসন' : 'Active Mason'
                        )
                )
        ),

    Verified_Lifting: DataStore.language == 'English' ?
        'Verified Lifting' : (
            DataStore.language == 'Hindi' ?
                'सत्यापित लिफ्टिंग' : (
                    DataStore.language == 'Assamese' ?
                        'পৰীক্ষা কৰা প্ৰত্যাহাৰ' : (
                            DataStore.language == 'Bengali' ?
                                'যাচাইকৃত উত্তোলন' : 'Verified Lifting'
                        )
                )
        ),

    Unverified_Lifting: DataStore.language == 'English' ?
        'Unverified Lifting' : (
            DataStore.language == 'Hindi' ?
                'असत्यापित भारोत्तोलन' : (
                    DataStore.language == 'Assamese' ?
                        'পৰীক্ষা নকৰা ওজন উত্তোলন' : (
                            DataStore.language == 'Bengali' ?
                                'অযাচাইকৃত ওজন উত্তোলন' : 'Unverified Lifting'
                        )
                )
        ),

    Total_Ppc_Lifting_Bag: DataStore.language == 'English' ?
        'Total Ppc Lifting Bag' : (
            DataStore.language == 'Hindi' ?
                'कुल पीपीसी लिफ्टिंग बैग' : (
                    DataStore.language == 'Assamese' ?
                        'মুঠ Ppc উত্তোলন বেগ' : (
                            DataStore.language == 'Bengali' ?
                                'মোট পিপিসি লিফটিং ব্যাগ' : 'Total Ppc Lifting Bag'
                        )
                )
        ),

    Total_Arc_Lifting_Bag: DataStore.language == 'English' ?
        'Total Arc Lifting Bag' : (
            DataStore.language == 'Hindi' ?
                'टोटल आर्क लिफ्टिंग बैग' : (
                    DataStore.language == 'Assamese' ?
                        'মুঠ আৰ্ক লিফটিং বেগ' : (
                            DataStore.language == 'Bengali' ?
                                'মোট আর্ক লিফটিং ব্যাগ' : 'Total Arc Lifting Bag'
                        )
                )
        ),

    Mason_Net_Point: DataStore.language == 'English' ?
        'Mason Net Point' : (
            DataStore.language == 'Hindi' ?
                'मेसन नेट प्वाइंट' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন নেট পইণ্ট' : (
                            DataStore.language == 'Bengali' ?
                                'মেসন নেট পয়েন্ট' : 'Mason Net Point'
                        )
                )
        ),

    Gift_Redeemed: DataStore.language == 'English' ?
        'Gift Redeemed' : (
            DataStore.language == 'Hindi' ?
                'उपहार भुनाया गया' : (
                    DataStore.language == 'Assamese' ?
                        'উপহাৰ ৰিডিম কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'উপহার খালাস' : 'Gift Redeemed'
                        )
                )
        ),

    Gift_pending: DataStore.language == 'English' ?
        'Gift pending' : (
            DataStore.language == 'Hindi' ?
                'उपहार लंबित है' : (
                    DataStore.language == 'Assamese' ?
                        'উপহাৰ বাকী আছে' : (
                            DataStore.language == 'Bengali' ?
                                'উপহার মুলতুবি' : 'Gift pending'
                        )
                )
        ),

    Gift_Delivered: DataStore.language == 'English' ?
        'Gift Delivered' : (
            DataStore.language == 'Hindi' ?
                'उपहार वितरित किया गया' : (
                    DataStore.language == 'Assamese' ?
                        'উপহাৰ ডেলিভাৰী কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'উপহার বিতরণ করা হয়েছে' : 'Gift Delivered'
                        )
                )
        ),

    Query_Raised: DataStore.language == 'English' ?
        'Query Raised' : (
            DataStore.language == 'Hindi' ?
                'प्रश्न उठाया' : (
                    DataStore.language == 'Assamese' ?
                        'প্ৰশ্ন উত্থাপন কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'প্রশ্ন উত্থাপিত' : 'Query Raised'
                        )
                )
        ),

    Query_pending: DataStore.language == 'English' ?
        'Query pending' : (
            DataStore.language == 'Hindi' ?
                'क्वेरी लंबित है' : (
                    DataStore.language == 'Assamese' ?
                        'প্ৰশ্ন বাকী আছে' : (
                            DataStore.language == 'Bengali' ?
                                'প্রশ্ন মুলতুবি' : 'Query pending'
                        )
                )
        ),

    Query_resolved: DataStore.language == 'English' ?
        'Query resolved' : (
            DataStore.language == 'Hindi' ?
                'क्वेरी का समाधान हो गया' : (
                    DataStore.language == 'Assamese' ?
                        'প্ৰশ্নৰ সমাধান হ’ল' : (
                            DataStore.language == 'Bengali' ?
                                'প্রশ্ন সমাধান করা হয়েছে' : 'Query resolved'
                        )
                )
        ),

    Dealer: DataStore.language == 'English' ?
        'Dealer' : (
            DataStore.language == 'Hindi' ?
                'विक्रेता' : (
                    DataStore.language == 'Assamese' ?
                        'ডিলাৰ' : (
                            DataStore.language == 'Bengali' ?
                                'ডিলার' : 'Dealer'
                        )
                )
        ),

    Mason_mobile: DataStore.language == 'English' ?
        'Mason mobile' : (
            DataStore.language == 'Hindi' ?
                'मेसन मोबाइल' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন মোবাইল' : (
                            DataStore.language == 'Bengali' ?
                                'মেসন মোবাইল' : 'Mason mobile'
                        )
                )
        ),

    Mason_branch: DataStore.language == 'English' ?
        'Mason branch' : (
            DataStore.language == 'Hindi' ?
                'मेसन शाखा' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন শাখা' : (
                            DataStore.language == 'Bengali' ?
                                'মেসন শাখা' : 'Mason branch'
                        )
                )
        ),

    Te_Code: DataStore.language == 'English' ?
        'Te Code' : (
            DataStore.language == 'Hindi' ?
                'ते कोड' : (
                    DataStore.language == 'Assamese' ?
                        "তে ক'ড" : (
                            DataStore.language == 'Bengali' ?
                                'টি কোড' : 'Te Code'
                        )
                )
        ),

    Te_Name: DataStore.language == 'English' ?
        'Te Name' : (
            DataStore.language == 'Hindi' ?
                'ते नाम' : (
                    DataStore.language == 'Assamese' ?
                        'তে নাম' : (
                            DataStore.language == 'Bengali' ?
                                'তে নাম' : 'Te Name'
                        )
                )
        ),

    Te_Phone: DataStore.language == 'English' ?
        'Te Phone' : (
            DataStore.language == 'Hindi' ?
                'ते फोन' : (
                    DataStore.language == 'Assamese' ?
                        'তে ফোন' : (
                            DataStore.language == 'Bengali' ?
                                'টে ফোন' : 'Te Phone'
                        )
                )
        ),

    Zone: DataStore.language == 'English' ?
        'Zone' : (
            DataStore.language == 'Hindi' ?
                'जोन' : (
                    DataStore.language == 'Assamese' ?
                        "জ'ন" : (
                            DataStore.language == 'Bengali' ?
                                'জোন' : 'Zone'
                        )
                )
        ),

    Product_name: DataStore.language == 'English' ?
        'Product name' : (
            DataStore.language == 'Hindi' ?
                'प्रोडक्ट का नाम' : (
                    DataStore.language == 'Assamese' ?
                        'প্ৰডাক্টৰ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'পণ্যের নাম' : 'Product name'
                        )
                )
        ),

    Product_quantity: DataStore.language == 'English' ?
        'Product quantity' : (
            DataStore.language == 'Hindi' ?
                'उत्पाद गुणवत्ता' : (
                    DataStore.language == 'Assamese' ?
                        'পণ্যৰ পৰিমাণ' : (
                            DataStore.language == 'Bengali' ?
                                'পণ্যের পরিমাণ' : 'Product quantity'
                        )
                )
        ),

    Verified_by: DataStore.language == 'English' ?
        'Verified by' : (
            DataStore.language == 'Hindi' ?
                'द्वारा सत्यापित' : (
                    DataStore.language == 'Assamese' ?
                        'দ্বাৰা পৰীক্ষা কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'দ্বারা যাচাই করা হয়েছে' : 'Verified by'
                        )
                )
        ),

    Adhaar: DataStore.language == 'English' ?
        'Adhaar' : (
            DataStore.language == 'Hindi' ?
                'आधार' : (
                    DataStore.language == 'Assamese' ?
                        'আধাৰ' : (
                            DataStore.language == 'Bengali' ?
                                'আধার' : 'Adhaar'
                        )
                )
        ),

    DOB: DataStore.language == 'English' ?
        'DOB' : (
            DataStore.language == 'Hindi' ?
                'जन्म तिथि' : (
                    DataStore.language == 'Assamese' ?
                        'জন্ম তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'জন্ম তারিখ' : 'DOB'
                        )
                )
        ),

    Marrital_Status: DataStore.language == 'English' ?
        'Marrital Status' : (
            DataStore.language == 'Hindi' ?
                'वैवाहिक स्थिति' : (
                    DataStore.language == 'Assamese' ?
                        'বৈবাহিক অৱস্থা' : (
                            DataStore.language == 'Bengali' ?
                                'বৈবাহিক অবস্থা' : 'Marrital Status'
                        )
                )
        ),

    Spouce_Name: DataStore.language == 'English' ?
        'Spouce Name' : (
            DataStore.language == 'Hindi' ?
                'जीवनसाथी का नाम' : (
                    DataStore.language == 'Assamese' ?
                        'পত্নীৰ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'পত্নীর নাম' : 'Spouce Name'
                        )
                )
        ),

    Spouce_DOB: DataStore.language == 'English' ?
        'Spouce DOB' : (
            DataStore.language == 'Hindi' ?
                'जीवनसाथी की जन्मतिथि' : (
                    DataStore.language == 'Assamese' ?
                        'পত্নী জন্ম তাৰিখ' : (
                            DataStore.language == 'Bengali' ?
                                'স্ত্রীর জন্ম তারিখ' : 'Spouce DOB'
                        )
                )
        ),

    Branch_Name: DataStore.language == 'English' ?
        'Branch Name' : (
            DataStore.language == 'Hindi' ?
                'शाखा का नाम' : (
                    DataStore.language == 'Assamese' ?
                        'শাখাৰ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'শাখার নাম' : 'Branch Name'
                        )
                )
        ),

    Zone_Name: DataStore.language == 'English' ?
        'Zone Name' : (
            DataStore.language == 'Hindi' ?
                'जोन का नाम' : (
                    DataStore.language == 'Assamese' ?
                        "জ'নৰ নাম" : (
                            DataStore.language == 'Bengali' ?
                                'জোনের নাম' : 'Zone Name'
                        )
                )
        ),

    Created_By: DataStore.language == 'English' ?
        'Created By' : (
            DataStore.language == 'Hindi' ?
                'के द्वारा बनाई गई' : (
                    DataStore.language == 'Assamese' ?
                        'দ্বাৰা সৃষ্টি কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'দ্বারা নির্মিত' : 'Created By'
                        )
                )
        ),

    Linked_TE_Name : DataStore.language == 'English' ?
        'Linked TE Name' : (
            DataStore.language == 'Hindi' ?
                'लिंक किया गया TE नाम' : (
                    DataStore.language == 'Assamese' ?
                        'লিংক কৰা টি ই নাম' : (
                            DataStore.language == 'Bengali' ?
                                'লিঙ্কড টিই নাম' : 'Linked TE Name'
                        )
                )
        ),

    Mason_category: DataStore.language == 'English' ?
        'Mason category' : (
            DataStore.language == 'Hindi' ?
                'मेसन श्रेणी' : (
                    DataStore.language == 'Assamese' ?
                        'মেছন শ্ৰেণী' : (
                            DataStore.language == 'Bengali' ?
                                'মেসন বিভাগ' : 'Mason category'
                        )
                )
        ),

    Order_no: DataStore.language == 'English' ?
        'Order no' : (
            DataStore.language == 'Hindi' ?
                'आदेश संख्या' : (
                    DataStore.language == 'Assamese' ?
                        'অৰ্ডাৰ নং' : (
                            DataStore.language == 'Bengali' ?
                                'অর্ডার নং' : 'Order no'
                        )
                )
        ),

    Employee_name: DataStore.language == 'English' ?
        'Employee name' : (
            DataStore.language == 'Hindi' ?
                'कर्मचारी का नाम' : (
                    DataStore.language == 'Assamese' ?
                        'কৰ্মচাৰীৰ নাম' : (
                            DataStore.language == 'Bengali' ?
                                'কর্মচারীর নাম' : 'Employee name'
                        )
                )
        ),

    Catalogue: DataStore.language == 'English' ?
        'Catalogue' : (
            DataStore.language == 'Hindi' ?
                'सूची' : (
                    DataStore.language == 'Assamese' ?
                        'কেটেলগ' : (
                            DataStore.language == 'Bengali' ?
                                'ক্যাটালগ' : 'Catalogue'
                        )
                )
        ),

    Type: DataStore.language == 'English' ?
        'Type' : (
            DataStore.language == 'Hindi' ?
                'प्रकार' : (
                    DataStore.language == 'Assamese' ?
                        'প্ৰকাৰ' : (
                            DataStore.language == 'Bengali' ?
                                'টাইপ' : 'Type'
                        )
                )
        ),

    Updated_at: DataStore.language == 'English' ?
        'Updated at' : (
            DataStore.language == 'Hindi' ?
                'पर अद्यतन किया गया' : (
                    DataStore.language == 'Assamese' ?
                        'আপডেট কৰা হৈছে' : (
                            DataStore.language == 'Bengali' ?
                                'এ আপডেট করা হয়েছে' : 'Updated at'
                        )
                )
        ),

    DASHBOARD_DETAILS: DataStore.language == 'English' ?
        'DASHBOARD DETAILS' : (
            DataStore.language == 'Hindi' ?
                'डैशबोर्ड विवरण' : (
                    DataStore.language == 'Assamese' ?
                        'ডেচবৰ্ডৰ বিৱৰণ' : (
                            DataStore.language == 'Bengali' ?
                                'ড্যাশবোর্ডের বিবরণ' : 'DASHBOARD DETAILS'
                        )
                )
        ),

    PHONE_NO: DataStore.language == 'English' ?
        'PHONE NO.' : (
            DataStore.language == 'Hindi' ?
                'फोन नंबर।' : (
                    DataStore.language == 'Assamese' ?
                        'ফোন নম্বৰ।' : (
                            DataStore.language == 'Bengali' ?
                                'ফোন নং' : 'PHONE NO.'
                        )
                )
        ),

    Select_Dealer_Rssd_Sub_Dealer: DataStore.language == 'English' ?
        'Select Dealer/Rssd/Sub-Dealer' : (
            DataStore.language == 'Hindi' ?
                'डीलर/आरएसएसडी/उप-डीलर का चयन करें' : (
                    DataStore.language == 'Assamese' ?
                        'ডিলাৰ/Rssd/Sub-Dealer নিৰ্বাচন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'ডিলার/আরএসএসডি/সাব-ডিলার নির্বাচন করুন' : 'Select Dealer/Rssd/Sub-Dealer'
                        )
                )
        ),

    ENTER_OTP: DataStore.language == 'English' ?
        'ENTER OTP' : (
            DataStore.language == 'Hindi' ?
                'फिर दर्ज करें' : (
                    DataStore.language == 'Assamese' ?
                        'তেতিয়া প্ৰৱেশ কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'ওটিপি লিখুন' : 'ENTER OTP'
                        )
                )
        ),

    Verify_from_OTP: DataStore.language == 'English' ?
        'Verify from OTP' : (
            DataStore.language == 'Hindi' ?
                'यहां से सत्यापित करें' : (
                    DataStore.language == 'Assamese' ?
                        'ইয়াৰ পৰা পৰীক্ষা কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'OTP থেকে যাচাই করুন' : 'Verify from OTP'
                        )
                )
        ),

    Verify_from_Star_Saathi: DataStore.language == 'English' ?
        'Verify from Star Saathi' : (
            DataStore.language == 'Hindi' ?
                'स्टार मेट से सत्यापित करें' : (
                    DataStore.language == 'Assamese' ?
                        'ষ্টাৰ মেটৰ পৰা পৰীক্ষা কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'স্টার সাথী থেকে যাচাই করুন' : 'Verify from Star Saathi'
                        )
                )
        ),

    DELIVERY_ADDRESS: DataStore.language == 'English' ?
        'DELIVERY ADDRESS' : (
            DataStore.language == 'Hindi' ?
                'डिलिवरी का पता' : (
                    DataStore.language == 'Assamese' ?
                        'ডেলিভাৰীৰ ঠিকনা' : (
                            DataStore.language == 'Bengali' ?
                                'ডেলিভারি ঠিকানা' : 'DELIVERY ADDRESS'
                        )
                )
        ),

    Change_delivery_address: DataStore.language == 'English' ?
        'Change delivery address' : (
            DataStore.language == 'Hindi' ?
                'डिलीवरी पता बदलें' : (
                    DataStore.language == 'Assamese' ?
                        'ডেলিভাৰীৰ ঠিকনা সলনি কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'বিতরণ ঠিকানা পরিবর্তন করুন' : 'Change delivery address'
                        )
                )
        ),

    Confirm: DataStore.language == 'English' ?
        'Confirm' : (
            DataStore.language == 'Hindi' ?
                'पुष्टि करना' : (
                    DataStore.language == 'Assamese' ?
                        'নিশ্চিত' : (
                            DataStore.language == 'Bengali' ?
                                'নিশ্চিত করুন' : 'Confirm'
                        )
                )
        ),

    Select_options: DataStore.language == 'English' ?
        'Select options' : (
            DataStore.language == 'Hindi' ?
                'विकल्प चुनें' : (
                    DataStore.language == 'Assamese' ?
                        'বিকল্পসমূহ নিৰ্ব্বাচন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'বিকল্প নির্বাচন করুন' : 'Select options'
                        )
                )
        ),

    Camera: DataStore.language == 'English' ?
        'Camera' : (
            DataStore.language == 'Hindi' ?
                'कैमरा' : (
                    DataStore.language == 'Assamese' ?
                        'কেমেৰাটো' : (
                            DataStore.language == 'Bengali' ?
                                'ক্যামেরা' : 'Camera'
                        )
                )
        ),

    Library: DataStore.language == 'English' ?
        'Library' : (
            DataStore.language == 'Hindi' ?
                'फ़ाइल' : (
                    DataStore.language == 'Assamese' ?
                        'ফাইল' : (
                            DataStore.language == 'Bengali' ?
                                'ফাইল' : 'Library'
                        )
                )
        ),

    Close: DataStore.language == 'English' ?
        'Close' : (
            DataStore.language == 'Hindi' ?
                'बंद करना' : (
                    DataStore.language == 'Assamese' ?
                        'বন্ধ' : (
                            DataStore.language == 'Bengali' ?
                                'বন্ধ' : 'Close'
                        )
                )
        ),

    No_Internet_connection: DataStore.language == 'English' ?
        'No Internet Connection' : (
            DataStore.language == 'Hindi' ?
                'कोई इंटरनेट कनेक्शन नहीं' : (
                    DataStore.language == 'Assamese' ?
                        'ইণ্টাৰনেট সংযোগ নাই' : (
                            DataStore.language == 'Bengali' ?
                                'ইন্টারনেট সংযোগ নেই' : 'No Internet Connection'
                        )
                )
        ),
        
    Select_Language: DataStore.language == 'English' ?
        'Select Language' : (
            DataStore.language == 'Hindi' ?
                'भाषा चुने' : (
                    DataStore.language == 'Assamese' ?
                        'ভাষা নিৰ্বাচন কৰক' : (
                            DataStore.language == 'Bengali' ?
                                'ভাষা নির্বাচন করুন' : 'Select Language'
                        )
                )
        ),
}
export default textValue