import DataStore from './DataStore'

let messageList={
    error:DataStore.language == 'English' ?
    'Error' : (
        DataStore.language == 'Hindi' ?
            'गलती' : (
                DataStore.language == 'Assamese' ?
                    'আঁসোৱাহ' : (
                        DataStore.language == 'Bengali' ?
                            'ত্রুটি' : 'Error'
                    )
            )
    ),
    
    success:DataStore.language == 'English' ?
    'Success' : (
        DataStore.language == 'Hindi' ?
            'सफलता' : (
                DataStore.language == 'Assamese' ?
                    'সফলতা' : (
                        DataStore.language == 'Bengali' ?
                            'সফলতা' : 'Success'
                    )
            )
    ),
    
    exit_app:DataStore.language == 'English' ?
    'Exit App' : (
        DataStore.language == 'Hindi' ?
            'ऐप से बाहर निकलें' : (
                DataStore.language == 'Assamese' ?
                    'এপৰ পৰা ওলাই যাওক' : (
                        DataStore.language == 'Bengali' ?
                            'অ্যাপ থেকে প্রস্থান করুন' : 'Exit App'
                    )
            )
    ),
    
    
    t1:DataStore.language == 'English' ?
    'This app would like to access your camera' : (
        DataStore.language == 'Hindi' ?
            'यह ऐप आपके कैमरे तक पहुंच बनाना चाहेगा' : (
                DataStore.language == 'Assamese' ?
                    'এই এপটোৱে আপোনাৰ কেমেৰাত প্ৰৱেশ কৰিব বিচাৰিব' : (
                        DataStore.language == 'Bengali' ?
                            'এই অ্যাপটি আপনার ক্যামেরা অ্যাক্সেস করতে চায়' : 'This app would like to access your camera'
                    )
            )
    ),

    t2:DataStore.language == 'English' ?
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

    t3:DataStore.language == 'English' ?
    'Are you sure to delete your account?' : (
        DataStore.language == 'Hindi' ?
            'क्या आप निश्चित रूप से अपना खाता हटाना चाहते हैं?' : (
                DataStore.language == 'Assamese' ?
                    'আপুনি নিশ্চিতভাৱে আপোনাৰ একাউণ্টটো মচি পেলাবনে?' : (
                        DataStore.language == 'Bengali' ?
                            'আপনি আপনার অ্যাকাউন্ট মুছে ফেলার বিষয়ে নিশ্চিত?' : 'Are you sure to delete your account?'
                    )
            )
    ),

    t4:DataStore.language == 'English' ?
    'Unknown error has occurred.' : (
        DataStore.language == 'Hindi' ?
            'अज्ञात त्रुटि हुई है.' : (
                DataStore.language == 'Assamese' ?
                    'অজ্ঞাত ভুল হৈছে।' : (
                        DataStore.language == 'Bengali' ?
                            'অজানা ত্রুটি ঘটেছে.' : 'Unknown error has occurred.'
                    )
            )
    ),
    
    t5:DataStore.language == 'English' ?
    'Do you want to exit this application?' : (
        DataStore.language == 'Hindi' ?
            'क्या आप इस एप्लिकेशन से बाहर निकलना चाहते हैं?' : (
                DataStore.language == 'Assamese' ?
                    'আপুনি এই এপ্লিকেচনৰ পৰা ওলাই আহিব বিচাৰেনে?' : (
                        DataStore.language == 'Bengali' ?
                            'আপনি কি এই অ্যাপ্লিকেশন থেকে প্রস্থান করতে চান?' : 'Do you want to exit this application?'
                    )
            )
    ),
    
    t6:DataStore.language == 'English' ?
    'Please enter your phone number.' : (
        DataStore.language == 'Hindi' ?
            'कृपया अपना फोन नंबर दर्ज करें ।' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি আপোনাৰ ফোন নম্বৰ দিয়ক ৷' : (
                        DataStore.language == 'Bengali' ?
                            'আপনার ফোন নম্বর লিখুন.' : 'Please enter your phone number.'
                    )
            )
    ),
    
    t7:DataStore.language == 'English' ?
    'New version available' : (
        DataStore.language == 'Hindi' ?
            'नया संस्करण उपलब्ध है' : (
                DataStore.language == 'Assamese' ?
                    'নতুন সংস্কৰণ উপলব্ধ' : (
                        DataStore.language == 'Bengali' ?
                            'নতুন সংস্করণ উপলব্ধ' : 'New version available'
                    )
            )
    ),
    
    t8:DataStore.language == 'English' ?
    'Please install the latest build from the Google Play Store' : (
        DataStore.language == 'Hindi' ?
            'कृपया Google Play Store से नवीनतम बिल्ड इंस्टॉल करें' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি Google Play Store ৰ পৰা শেহতীয়া বিল্ডটো ইনষ্টল কৰক' : (
                        DataStore.language == 'Bengali' ?
                            'অনুগ্রহ করে Google Play Store থেকে সর্বশেষ বিল্ডটি ইনস্টল করুন' : 'Please install the latest build from the Google Play Store'
                    )
            )
    ),
    
    t9:DataStore.language == 'English' ?
    'Please install the latest build from the App Store' : (
        DataStore.language == 'Hindi' ?
            'कृपया App Store से नवीनतम बिल्ड इंस्टॉल करें' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি App Store ৰ পৰা শেহতীয়া বিল্ডটো ইনষ্টল কৰক' : (
                        DataStore.language == 'Bengali' ?
                            'অনুগ্রহ করে App Store থেকে সর্বশেষ বিল্ডটি ইনস্টল করুন' : 'Please install the latest build from the App Store'
                    )
            )
    ),
    
    t10:DataStore.language == 'English' ?
    'Please enter your OTP.' : (
        DataStore.language == 'Hindi' ?
            'कृपया अपना ओटीपी दर्ज करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি আপোনাৰ OTP লিখক।' : (
                        DataStore.language == 'Bengali' ?
                            'অনুগ্রহ করে আপনার ওটিপি লিখুন।' : 'Please enter your OTP.'
                    )
            )
    ),
    
    t11:DataStore.language == 'English' ?
    'OTP should be 4 digits.' : (
        DataStore.language == 'Hindi' ?
            'ओटीपी 4 अंकों का होना चाहिए.' : (
                DataStore.language == 'Assamese' ?
                    'অ’টিপি ৪ সংখ্যা হ’ব লাগে।' : (
                        DataStore.language == 'Bengali' ?
                            'OTP 4 সংখ্যার হওয়া উচিত।' : 'OTP should be 4 digits.'
                    )
            )
    ),
    
    t12:DataStore.language == 'English' ?
    'Name is required.' : (
        DataStore.language == 'Hindi' ?
            'नाम आवश्यक है।' : (
                DataStore.language == 'Assamese' ?
                    'নাম দৰকাৰ।' : (
                        DataStore.language == 'Bengali' ?
                            'নাম আবশ্যক.' : 'Name is required.'
                    )
            )
    ),
    
    t13:DataStore.language == 'English' ?
    'Phone no is required.' : (
        DataStore.language == 'Hindi' ?
            'फ़ोन नंबर आवश्यक है.' : (
                DataStore.language == 'Assamese' ?
                    'ফোন নংৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'ফোন নম্বর প্রয়োজন.' : 'Phone no is required.'
                    )
            )
    ),
    
    t14:DataStore.language == 'English' ?
    'Please select material status.' : (
        DataStore.language == 'Hindi' ?
            'कृपया भौतिक स्थिति का चयन करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি সামগ্ৰীৰ অৱস্থা নিৰ্বাচন কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'উপাদান স্থিতি নির্বাচন করুন.' : 'Please select material status.'
                    )
            )
    ),
    
    t15:DataStore.language == 'English' ?
    'Please select branch.' : (
        DataStore.language == 'Hindi' ?
            'कृपया शाखा का चयन करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি শাখা নিৰ্বাচন কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'অনুগ্রহ করে শাখা নির্বাচন করুন।' : 'Please select branch.'
                    )
            )
    ),
    
    t16:DataStore.language == 'English' ?
    'Please select Dealer/RSSD.' : (
        DataStore.language == 'Hindi' ?
            'कृपया डीलर/आरएसएसडी का चयन करें।' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি ডিলাৰ/RSSD নিৰ্বাচন কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'অনুগ্রহ করে ডিলার/RSSD নির্বাচন করুন।' : 'Please select Dealer/RSSD.'
                    )
            )
    ),
    
    t17:DataStore.language == 'English' ?
    'Please select technical engineer.' : (
        DataStore.language == 'Hindi' ?
            'कृपया तकनीकी इंजीनियर का चयन करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি কাৰিকৰী অভিযন্তা বাছনি কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'প্রযুক্তিগত প্রকৌশলী নির্বাচন করুন.' : 'Please select technical engineer.'
                    )
            )
    ),
    
    t18:DataStore.language == 'English' ?
    'Addres 1 is required.' : (
        DataStore.language == 'Hindi' ?
            'पता 1 आवश्यक है.' : (
                DataStore.language == 'Assamese' ?
                    'ঠিকনা ১ ৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'ঠিকানা 1 প্রয়োজন.' : 'Addres 1 is required.'
                    )
            )
    ),
    
    t19:DataStore.language == 'English' ?
    'City is required.' : (
        DataStore.language == 'Hindi' ?
            'शहर की आवश्यकता है.' : (
                DataStore.language == 'Assamese' ?
                    'চহৰৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'শহর প্রয়োজন.' : 'City is required.'
                    )
            )
    ),
    
    t20:DataStore.language == 'English' ?
    'District is required.' : (
        DataStore.language == 'Hindi' ?
            'जिला आवश्यक है.' : (
                DataStore.language == 'Assamese' ?
                    'জিলাৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'জেলা প্রয়োজন।' : 'District is required.'
                    )
            )
    ),
    
    t21:DataStore.language == 'English' ?
    'State is required.' : (
        DataStore.language == 'Hindi' ?
            'राज्य की आवश्यकता है' : (
                DataStore.language == 'Assamese' ?
                    'ৰাজ্যৰ প্ৰয়োজন' : (
                        DataStore.language == 'Bengali' ?
                            'রাজ্য প্রয়োজন.' : 'State is required.'
                    )
            )
    ),
    
    t22:DataStore.language == 'English' ?
    'Country is required.' : (
        DataStore.language == 'Hindi' ?
            'देश चाहिए.' : (
                DataStore.language == 'Assamese' ?
                    'দেশৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'দেশ প্রয়োজন.' : 'Country is required.'
                    )
            )
    ),
    
    t23:DataStore.language == 'English' ?
    'Pin code is required.' : (
        DataStore.language == 'Hindi' ?
            'पिन कोड आवश्यक है.' : (
                DataStore.language == 'Assamese' ?
                    'পিন ক’ডৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'পিন কোড প্রয়োজন.' : 'Pin code is required.'
                    )
            )
    ),
    
    t24:DataStore.language == 'English' ?
    'DOB is required.' : (
        DataStore.language == 'Hindi' ?
            'जन्मतिथि आवश्यक है.' : (
                DataStore.language == 'Assamese' ?
                    'জন্ম তাৰিখৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'জন্ম তারিখ প্রয়োজন।' : 'DOB is required.'
                    )
            )
    ),
    
    t25:DataStore.language == 'English' ?
    'Aadhaar is required.' : (
        DataStore.language == 'Hindi' ?
            'आधार जरूरी है.' : (
                DataStore.language == 'Assamese' ?
                    'আধাৰৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'আধার প্রয়োজন।' : 'Aadhaar is required.'
                    )
            )
    ),
    
    t26:DataStore.language == 'English' ?
    'Aadhaar number should be 12 digits.' : (
        DataStore.language == 'Hindi' ?
            'आधार नंबर 12 अंकों का होना चाहिए.' : (
                DataStore.language == 'Assamese' ?
                    'আধাৰ নম্বৰ ১২ অংক হব লাগে।' : (
                        DataStore.language == 'Bengali' ?
                            'আধার নম্বরটি 12 সংখ্যার হওয়া উচিত।' : 'Aadhaar number should be 12 digits.'
                    )
            )
    ),
    
    t27:DataStore.language == 'English' ?
    'Please browse Aadhaar card.' : (
        DataStore.language == 'Hindi' ?
            'कृपया आधार कार्ड ब्राउज़ करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি আধাৰ কাৰ্ড ব্ৰাউজ কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'অনুগ্রহ করে আধার কার্ড ব্রাউজ করুন।' : 'Please browse Aadhaar card.'
                    )
            )
    ),
    
    t28:DataStore.language == 'English' ?
    'Employee code is required.' : (
        DataStore.language == 'Hindi' ?
            'कर्मचारी कोड आवश्यक है.' : (
                DataStore.language == 'Assamese' ?
                    "কৰ্মচাৰীৰ ক'ড প্ৰয়োজনীয়।" : (
                        DataStore.language == 'Bengali' ?
                            'কর্মচারী কোড প্রয়োজন.' : 'Employee code is required.'
                    )
            )
    ),
    
    t29:DataStore.language == 'English' ?
    'Please select branches' : (
        DataStore.language == 'Hindi' ?
            'कृपया शाखाएँ चुनें' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি শাখা নিৰ্বাচন কৰক' : (
                        DataStore.language == 'Bengali' ?
                            'শাখা নির্বাচন করুন' : 'Please select branches'
                    )
            )
    ),
    
    t30:DataStore.language == 'English' ?
    'Please select dealer/rssd/sub-dealer.' : (
        DataStore.language == 'Hindi' ?
            'कृपया डीलर/आरएसएसडी/उप-डीलर का चयन करें।' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি ডিলাৰ/rssd/চাব-ডিলাৰ নিৰ্বাচন কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'অনুগ্রহ করে ডিলার/আরএসএসডি/সাব-ডিলার নির্বাচন করুন।' : 'Please select dealer/rssd/sub-dealer.'
                    )
            )
    ),
    
    t31:DataStore.language == 'English' ?
    'Please select product.' : (
        DataStore.language == 'Hindi' ?
            'कृपया उत्पाद चुनें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি প্ৰডাক্ট নিৰ্বাচন কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'পণ্য নির্বাচন করুন.' : 'Please select product.'
                    )
            )
    ),
    
    t32:DataStore.language == 'English' ?
    'Please enter quantity.' : (
        DataStore.language == 'Hindi' ?
            'कृपया मात्रा दर्ज करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি পৰিমাণ লিখক।' : (
                        DataStore.language == 'Bengali' ?
                            'পরিমাণ লিখুন দয়া করে.' : 'Please enter quantity.'
                    )
            )
    ),
    
    t33:DataStore.language == 'English' ?
    'Please enter valid quantity.' : (
        DataStore.language == 'Hindi' ?
            'कृपया वैध मात्रा दर्ज करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি বৈধ পৰিমাণ দিয়ক।' : (
                        DataStore.language == 'Bengali' ?
                            'বৈধ পরিমাণ লিখুন.' : 'Please enter valid quantity.'
                    )
            )
    ),
    
    t34:DataStore.language == 'English' ?
    'Datetime is required.' : (
        DataStore.language == 'Hindi' ?
            'दिनांकसमय आवश्यक है.' : (
                DataStore.language == 'Assamese' ?
                    'তাৰিখসময়ৰ প্ৰয়োজন।' : (
                        DataStore.language == 'Bengali' ?
                            'তারিখ সময় প্রয়োজন.' : 'Datetime is required.'
                    )
            )
    ),
    
    t35:DataStore.language == 'English' ?
    'Please enter messages.' : (
        DataStore.language == 'Hindi' ?
            'कृपया संदेश दर्ज करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি বাৰ্তা প্ৰৱেশ কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'বার্তা লিখুন.' : 'Please enter messages.'
                    )
            )
    ),
    
    t36:DataStore.language == 'English' ?
    'WhatsApp not installed on your device.' : (
        DataStore.language == 'Hindi' ?
            'आपके डिवाइस पर व्हाट्सएप इंस्टॉल नहीं है।' : (
                DataStore.language == 'Assamese' ?
                    'আপোনাৰ ডিভাইচত হোৱাটছএপ ইনষ্টল হোৱা নাই।' : (
                        DataStore.language == 'Bengali' ?
                            'আপনার ডিভাইসে WhatsApp ইনস্টল করা নেই।' : 'WhatsApp not installed on your device.'
                    )
            )
    ),
    
    t37:DataStore.language == 'English' ?
    'Please select any product.' : (
        DataStore.language == 'Hindi' ?
            'कृपया कोई भी उत्पाद चुनें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি যিকোনো সামগ্ৰী নিৰ্বাচন কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'যে কোন পণ্য নির্বাচন করুন.' : 'Please select any product.'
                    )
            )
    ),
    
    t38:DataStore.language == 'English' ?
    'Please select start date and end date' : (
        DataStore.language == 'Hindi' ?
            'कृपया आरंभ तिथि और समाप्ति तिथि चुनें' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি আৰম্ভণিৰ তাৰিখ আৰু শেষৰ তাৰিখ নিৰ্ব্বাচন কৰক' : (
                        DataStore.language == 'Bengali' ?
                            'অনুগ্রহ করে শুরুর তারিখ এবং শেষ তারিখ নির্বাচন করুন' : 'Please select start date and end date'
                    )
            )
    ),
    
    t39:DataStore.language == 'English' ?
    'Please verified the phone no.' : (
        DataStore.language == 'Hindi' ?
            'कृपया फ़ोन नंबर सत्यापित करें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি ফোন নং 10 ৰ ভেৰিফাইড কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'ফোন নম্বর যাচাই করুন.' : 'Please verified the phone no.'
                    )
            )
    ),
    
    t40:DataStore.language == 'English' ?
    'Please select your support reason.' : (
        DataStore.language == 'Hindi' ?
            'कृपया अपना समर्थन कारण चुनें.' : (
                DataStore.language == 'Assamese' ?
                    'অনুগ্ৰহ কৰি আপোনাৰ সমৰ্থন কাৰণ নিৰ্ব্বাচন কৰক।' : (
                        DataStore.language == 'Bengali' ?
                            'আপনার সমর্থন কারণ নির্বাচন করুন.' : 'Please select your support reason.'
                    )
            )
    ),
}

export default messageList