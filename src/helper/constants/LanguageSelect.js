import DataStore from './DataStore'

const selectedLanguage = () => {
    let language = 'en'
    switch (DataStore.language) {
        case 'English':
            language = 'en'
            break
        case 'Hindi':
            language = 'hi'
            break
        case 'Assamese':
            language = 'as'
            break
        case 'Bengali':
            language = 'bn'
            break
    }
    return language
}

export default selectedLanguage