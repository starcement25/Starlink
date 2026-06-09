import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container: { flex: 1, },
    _bgColor: { backgroundColor: '#ee1d23', flex: 1 },
    _upperView: {
        justifyContent: 'center', alignItems: 'center',
        _img: { height: 60, width: 60 },
        _txt: { color: '#000', fontSize: 16, fontWeight: '700' }
    },
    _lowerView: {
        backgroundColor: '#fff', flex: 1, borderTopRightRadius: 100, position: 'relative', justifyContent: 'center', alignItems: 'center',
        authIcon: { width: 40, height: 40 },
        _loginTxt: { color: '#ee1d23', fontSize: 22, fontWeight: '500', marginTop: '1%' },
        _input: {
            backgroundColor: '#F3F3F3', height: 50, width: '100%', borderRadius: 10, alignItems: 'center', borderColor: '#0000', borderWidth: 1, flexDirection: 'row', overflow: 'hidden',
            _img: { height: 15, width: 15, resizeMode: 'cover' },
            _input: { color: '#000', width: '80%', marginLeft: 5 }
        },
        _loginBtn: {
            backgroundColor: '#ee1d23', height: 50, width: '100%', borderRadius: 10, marginTop: '5%', justifyContent: 'center', alignItems: 'center',
            _txt: { color: '#fff', fontWeight: '600', fontSize: 18 }
        }
    },
    bg: { height: '60%', width: '90%', resizeMode: 'cover' },
})