<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class GoogleTranslateService
{
    protected $apiKey;

    public function __construct()
    {
        // Initialize the API Key
        $this->apiKey = env('GOOGLE_CLOUD_API_KEY');
    }

    public static function languageCodes(): array
    {
        return ["en", "hi", "bn", "as"];
    }

    // Function to translate text
    public function translateText($text, $targetLanguage = 'en')
    {
        try
        {
            // //When translation will open need to Uncomment it or remove it.
            return $text;

            if(empty($targetLanguage) || empty($text) || !in_array($targetLanguage, self::languageCodes()) || $targetLanguage == 'en')
            {
                return $text;
            }
            $response = Http::asForm()->post('https://translation.googleapis.com/language/translate/v2', [
                'q' => $text,
                'source' => "en",
                'target' => $targetLanguage,
                'key' => $this->apiKey
            ]);
            if ($response->successful()) {
                // Return the translated text
                return $response->json()['data']['translations'][0]['translatedText'];
            } else {
                // Handle errors or failed response
                // return 'Error: ' . $response->status();
                throw new \Exception('Error: ' . $response['error']['message']);
            }
        }
        catch(\Exception $e)
        {
            \Log::error($e->getMessage());
            return $text;
        }
    }
}
