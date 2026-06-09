import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container:
    {
        flex: 1,
    },
    _bgColor:
    {
        backgroundColor: '#ee1d23',
        position: 'relative',
        flex: 1
    },

    _upperView:
    {
        height: 150,
        position: 'relative',
        justifyContent: 'center',
        alignItems: 'center',

        _txt: {
            color: '#fff',
            fontSize: 20,
            fontWeight: '500'
        },
        _back: {
            position: 'absolute',
            top: 10,
            right: 10,

            _img: {
                height: 30,
                width: 30,
            }
        }
    },

    _lowerView:
    {
        flex: 1,
        backgroundColor: '#fff',
        borderTopRightRadius: 100,
        position: 'relative',
        overflow: 'hidden'
    },

})