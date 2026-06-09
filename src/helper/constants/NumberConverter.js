import DataStore from './DataStore'

export const convertForUploadData = (value) => {
    var convertData = ''
    Array.from(value).map((char) => {
        switch (char) {
            case '০':
            case '॰':
                convertData = convertData + '0'
                break
            case '১':
            case '१':
                convertData = convertData + '1'
                break
            case '২':
            case '२':
                convertData = convertData + '2'
                break
            case '৩':
            case '३':
                convertData = convertData + '3'
                break
            case '৪':
            case '४':
                convertData = convertData + '4'
                break
            case '৫':
            case '५':
                convertData = convertData + '5'
                break
            case '৬':
            case '६':
                convertData = convertData + '6'
                break
            case '৭':
            case '७':
                convertData = convertData + '7'
                break
            case '৮':
            case '८':
                convertData = convertData + '8'
                break
            case '৯':
            case '९':
                convertData = convertData + '9'
                break
            default:
                convertData = convertData + char
        }
    })
    return convertData
}

export const convertForShowData = (value) => {
    var convertData = ''
    if (value != undefined) {
        Array.from(value.toString()).map((char) => {
            switch (char) {
                case '0':
                case '॰':
                case '০':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '॰'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '০'
                            break
                        default:
                            convertData = convertData + '0'
                            break
                    }
                    break
                case '1':
                case '१':
                case '১':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '१'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '১'
                            break
                        default:
                            convertData = convertData + '1'
                            break
                    }
                    break
                case '2':
                case '२2':
                case '২':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '२'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '২'
                            break
                        default:
                            convertData = convertData + '2'
                            break
                    }
                    break
                case '3':
                case '३':
                case '৩':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '३'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '৩'
                            break
                        default:
                            convertData = convertData + '3'
                            break
                    }
                    break
                case '4':
                case '४':
                case '৪':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '४'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '৪'
                            break
                        default:
                            convertData = convertData + '4'
                            break
                    }
                    break
                case '5':
                case '५':
                case '৫':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '५'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '৫'
                            break
                        default:
                            convertData = convertData + '5'
                            break
                    }
                    break
                case '6':
                case '६':
                case '৬':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '६'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '৬'
                            break
                        default:
                            convertData = convertData + '6'
                            break
                    }
                    break
                case '7':
                case '७':
                case '৭':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '७'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '৭'
                            break
                        default:
                            convertData = convertData + '7'
                            break
                    }
                    break
                case '8':
                case '८':
                case '৮':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '८'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '৮'
                            break
                        default:
                            convertData = convertData + '8'
                            break
                    }
                    break
                case '9':
                case '९':
                case '৯':
                    switch (DataStore.language) {
                        case 'Hindi':
                            convertData = convertData + '९'
                            break
                        case 'Assamese':
                        case 'Bengali':
                            convertData = convertData + '৯'
                            break
                        default:
                            convertData = convertData + '9'
                            break
                    }
                    break
                default:
                    convertData = convertData + char
            }
        })
    }
    return convertData
}
