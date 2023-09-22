#include <bits/stdc++.h>
#include <atcoder/modint>
#include <atcoder/dsu>
#define rep(i, p, n) for (int i = p; i < (int)(n); i++)
using namespace std;
using namespace atcoder;
using ll = long long;
using mint1 = modint1000000007;
using mint2 = modint998244353;
double pi=3.141592653589793;
const long long inf=2*1e9;
const long long linf=8*1e18;
template<class T> inline bool chmax(T& a, T b) { if (a < b) { a = b; return 1; } return 0; }

//素数列挙
void SR(ll N, vector<ll> &sosuu) { 
    vector<bool> testSR(N+1, true);
    for(int i=2; i<=N; i++) {
        if (testSR.at(i)) {
            sosuu.push_back(i);
            for(int j=i*2; j<=N; j+=i) {
                testSR.at(j)=false;
            }
        }
    }
}

int main() {
    ll N;
    cin >> N;
    vector<ll> sosuu(0);
    ll P=sqrt(N);
    SR(P, sosuu);
    vector<pair<ll, ll>> soi(0);
    ll R=N;
    for(int c:sosuu) {
        ll co=0;
        while(true) {
            if (R%c==0) {
                co++;
                R/=c;
            }
            else {
                break;
            }
        }
        if (co!=0) {
            soi.push_back({c, co});
        }
    }
    ll ans=1;
    for(auto P:soi) {
        ll co=0, now=P.first;
        while(true) {
            ll Q=now;
            while(true) {
                if (Q%P.first==0) {
                    co++;
                    Q/=first;
                }
                else {
                    break;
                }
            }
            if (co>=P.second) {
                ans=max(ans, now);
                break;
            }
            else {
                now+=P.first;
            }
        }
    }
    cout << ans;
}