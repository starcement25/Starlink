import { StyleSheet } from 'react-native'
export default StyleSheet.create({
    container:
    {
        flex: 1,
    },
    _bgColor: {
        backgroundColor: '#ee1d23',
        flex: 1
    },
    _upperView: {
        height: 150,
        justifyContent: 'center',
        alignItems: 'center'
    },
    _circle: {
        height: 100,
        width: 100,
        backgroundColor: '#fff',
        borderRadius: 100,
        shadowColor: '#707070',
        shadowOffset: { width: 1, height: 1 },
        shadowOpacity: .4,
        shadowRadius: 8,
        justifyContent: 'center',
        alignItems: 'center',

        _img: {
            width: '100%',
            height: '100%'
        }
    },
    _lowerView: {
        backgroundColor: '#fff',
        flex: 1,
        borderTopRightRadius: 100,
        position: 'relative',
        justifyContent: 'center',
        alignItems: 'center',

        authIcon: {
            width: 40,
            height: 40
        },
        _loginTxt: {
            color: '#ee1d23',
            fontSize: 22,
            fontWeight: '500',
            marginTop: '1%'
        },
        _loginBtn: {
            backgroundColor: '#ee1d23',
            height: 35,
            width: '50%',
            borderRadius: 20,
            marginTop: '10%',
            justifyContent: 'center',
            alignItems: 'center',

            _txt: {
                color: '#fff',
                fontWeight: '600'
            }
        }
    },
    bg:
    {
        height: '70%',
        width: '100%',
        justifyContent: 'center',
        alignItems: 'center',
        position: 'absolute',
        bottom: 0,

        _img: {
            height: '85%',
            width: '85%',
            resizeMode: 'cover'
        }
    },

})
