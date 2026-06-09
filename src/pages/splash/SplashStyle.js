import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container:
    {
        flex: 1,
        backgroundColor: '#ee1d23',
        justifyContent: 'center',
        alignItems: 'center',
    },
    _circle: {
        height: 200,
        width: 200,
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

    _bags_view: {
        flexDirection: 'row',
        position: 'absolute',
        bottom: 15,
        right: 15,

        _img: {
            width: 200,
            height: 200,
            resizeMode: 'contain',
        }
    },

    _background: {
        height: '100%',
        width: '100%',
        resizeMode: 'cover'
    }
})
