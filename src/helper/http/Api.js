// import axios from 'axios'
// import constants from '../../helper/constants/Constants'
// import AsyncStorage from '@react-native-async-storage/async-storage'

// export async function getApiWithHeader(url) {
//     try {
//         const access_token = await AsyncStorage.getItem('access_token')

//         if (!access_token) {
//             throw {
//                 status: 401,
//                 message: 'Session expired. Please login again.',
//             }
//         }

//         // Detect my_profile API
//         const isMyProfileApi = url.includes('my_profile')

//         const headers = {
//             Accept: 'application/json',
//             Authorization: `Bearer ${access_token}`,
//         }

//         // Only add Content-Type when NOT my_profile
//         if (!isMyProfileApi) {
//             headers['Content-Type'] = 'multipart/form-data'
//         }

//         return await axios.get(`${constants.base_url}/${url}`, {
//             headers,
//         })
//     } catch (error) {
//         throw error
//     }
// }


// export async function getApiWithHeaderr(url) {
//     try {
//         const access_token = await AsyncStorage.getItem('access_token')
//         if (access_token !== null) {
//             return await axios.get(`${constants.base_url}/${url}`, {
//                 headers: {
//                     'Accept': 'application/json',
//                     'Content-type': 'multipart/form-data',
//                     'Authorization': 'Bearer ' + access_token,
//                 },
//             })
//         }
//     } catch (e) { }
// }

// export async function getApi(url) {
//     return await axios.get(`${constants.base_url}/${url}`, {
//         headers: {
//             'Content-type': 'multipart/form-data',
//         }
//     })
// }

// export function postApi(url, payload) {
//     return axios.post(`${constants.base_url}/${url}`, payload, {
//         headers: {
//             'Content-type': 'multipart/form-data',
//         }
//     })
// }

// export function postApiWithSingleHeader(url, payload) {
//     return axios.post(`${constants.base_url}/${url}`, payload, {
//         headers: {
//             'Accept': 'application/json',
//             'Content-type': 'multipart/form-data',
//         }
//     })
// }

// export async function postApiWithHeader(url, payload) {
//     try {
//         const access_token = await AsyncStorage.getItem('access_token')
//         if (access_token !== null) {
//             return await axios.post(`${constants.base_url}/${url}`, payload, {
//                 headers: {
//                     'Accept': 'application/json',
//                     'Content-type': 'multipart/form-data',
//                     'Authorization': 'Bearer ' + access_token,
//                 }
//             })
//         }
//     } catch (e) { }
// }

// export async function requestForCheckTextLanguage(array_of_text) {
//     return await axios.post(`https://translation.googleapis.com/language/translate/v2/detect?key=AIzaSyBGLsoao9R0m9mEYxVrvNWnSu2ullebn2I`, array_of_text, {
//         headers: {
//             'Accept': 'application/json',
//             'Content-type': 'application/json'
//         }
//     })
// }

// export async function requestForChangeTextLanguageToEnglish(array_of_text) {
//     const raw = JSON.stringify({
//         'q': array_of_text,
//         'target': 'en'
//     })
//     return await axios.post(`https://translation.googleapis.com/language/translate/v2?key=AIzaSyBGLsoao9R0m9mEYxVrvNWnSu2ullebn2I`, raw, {
//         headers: {
//             'Accept': 'application/json',
//             'Content-type': 'application/json'
//         }
//     })
// }


import axios from 'axios'
import constants from '../../helper/constants/Constants'
import AsyncStorage from '@react-native-async-storage/async-storage'

/**
 * Axios instance
 */
const api = axios.create({
    baseURL: constants.base_url,
    timeout: 20000, // 20s timeout
})


/**
 * Common error handler
 */
const handleError = (error, url) => {
    if (error.response) {
        // Server responded with a status code outside 2xx
        //console.log(`API ERROR [${url}]`, { status: error.response.status, data: error.response.data, })
       
        
        throw {
            status: error.response.status,
            data: error.response.data,
            message: error.response.data?.message || 'Something went wrong',
        }
    } else if (error.request) {
        // Request made but no response
        //console.log(`NETWORK ERROR [${url}]`, error.request)
        throw {
            status: null,
            data: null,
            message: 'Network error. Please check your internet connection.',
        }
    } else {
        // Something else happened
        //console.log(`UNKNOWN ERROR [${url}]`, error.message)
        throw {
            status: null,
            data: null,
            message: error.message || 'Unexpected error occurred',
        }
    }
}

/**
 * Get auth header
 */
const getAuthHeader = async () => {
    const token = await AsyncStorage.getItem('access_token')
    if (!token) {
        throw {
            status: 401,
            data: null,
            message: 'Access token missing',
        }
    }
    return {
        Authorization: `Bearer ${token}`,
    }
}

/**
 * GET (no auth)
 */
export async function getApi(url) {
    try {
        return await api.get(`/${url}`, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
    } catch (error) {
        throw handleError(error, url)
    }
}

/**
 * GET (with auth)
 */
export async function getApiWithHeader(url, retryCount = 0) {
  try {
    console.log('GET API 👉', constants.base_url + url)

    const authHeader = await getAuthHeader()

    const response = await api.get(url, {
      headers: {
        Accept: 'application/json',
        ...authHeader,
      },
    })

    // success
    if (response?.status === 200 || response?.data?.status_code === 200) {
      return response
    }

    // retry safeguard
    if (retryCount < 3) {
      //console.log(`🔁 Retry GET (${retryCount + 1}/3)`)
      await new Promise(res => setTimeout(res, 2000))
      return getApiWithHeader(url, retryCount + 1)
    }

    throw new Error('API failed after retries')

  } catch (error) {
    //console.log('❌ GET API ERROR:', error?.response || error?.message)
    //throw error   // IMPORTANT: let caller handle it
     await new Promise(res => setTimeout(res, 60000))
    return getApiWithHeader(url, retryCount + 1)
  }
}


/**
 * POST (no auth)
 */
export async function postApi(url, payload) {
    try {
        return await api.post(`/${url}`, payload, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
    } catch (error) {
        handleError(error, url)
    }
}

export async function postApiJSON(url, payload) {
  try {
    return await api.post(`/${url}`, payload, {
      headers: {
        'Content-Type': 'application/json',
      },
    })
  } catch (error) {
    throw handleError(error, url)
  }
}

/**
 * POST (single header)
 */
export async function postApiWithSingleHeader(url, payload) {
    try {
        return await api.post(`/${url}`, payload, {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'multipart/form-data',
            },
        })
    } catch (error) {
        handleError(error, url)
    }
}

/**
 * POST (with auth)
 */
export async function postApiWithHeader(url, payload) {
    try {
        const authHeader = await getAuthHeader()
        //console.log("token---", authHeader);
        
        return await api.post(`/${url}`, payload, {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'multipart/form-data',
                ...authHeader,
            },
        })
    } catch (error) {
        handleError(error, url)
    }
}

/**
 * Language detection
 */
export async function requestForCheckTextLanguage(array_of_text) {
    try {
        return await axios.post(
            'https://translation.googleapis.com/language/translate/v2/detect',
            array_of_text,
            {
                params: {
                    key: 'AIzaSyBGLsoao9R0m9mEYxVrvNWnSu2ullebn2I',
                },
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
            }
        )
    } catch (error) {
        handleError(error, 'language-detect')
    }
}

/**
 * Translate to English
 */
export async function requestForChangeTextLanguageToEnglish(array_of_text) {
    try {
        return await axios.post(
            'https://translation.googleapis.com/language/translate/v2',
            {
                q: array_of_text,
                target: 'en',
            },
            {
                params: {
                    key: 'AIzaSyBGLsoao9R0m9mEYxVrvNWnSu2ullebn2I',
                },
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
            }
        )
    } catch (error) {
        handleError(error, 'language-translate')
    }
}
export async function postApiWithHeaderBody(url, payload) {
  try {
    const authHeader = await getAuthHeader();
    //console.log("url---", url)

    return await api.post(`/${url}`, payload, {
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...authHeader,
      },
    });
  } catch (error) {
    handleError(error, url);
  }
}

